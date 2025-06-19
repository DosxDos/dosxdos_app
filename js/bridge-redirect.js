class UserBridge {
    constructor() {
        this.dbName = 'dosxdos';
        this.storeName = 'usuario';
        this.backendUrl = 'https://dosxdos.app.iidos.com/apirest/generate_bridge_token.php';
        this.nextjsUrl = 'http://nextjs.dosxdos.app/nextjs';
    }

    // Get user data from IndexedDB
    async getUserFromIndexedDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName);

            request.onerror = () => reject(request.error);

            request.onsuccess = () => {
                const db = request.result;
                const transaction = db.transaction([this.storeName], 'readonly');
                const store = transaction.objectStore(this.storeName);

                // Get all users (since you're storing an array)
                const getRequest = store.getAll();

                getRequest.onsuccess = () => {
                    if (getRequest.result && getRequest.result.length > 0) {
                        // Return the first user (current logged-in user)
                        resolve(getRequest.result[0]);
                    } else {
                        reject('User not found');
                    }
                };

                getRequest.onerror = () => reject(getRequest.error);
            };
        });
    }

    // Generate JWT token via backend
    async generateBridgeToken(userData) {
        try {
            console.log('Sending data to backend:', userData);

            const response = await fetch(this.backendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_data: userData
                })
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Get the raw response text first to debug
            const responseText = await response.text();
            console.log('Raw backend response:', responseText);

            // Try to parse as JSON
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                console.error('Failed to parse JSON response:', parseError);
                console.error('Response was:', responseText);
                throw new Error('Backend returned invalid JSON. Check PHP errors.');
            }

            if (result.success) {
                return result.token;
            } else {
                throw new Error(result.error || 'Failed to generate token');
            }
        } catch (error) {
            console.error('Error generating bridge token:', error);
            throw error;
        }
    }

    // Main function to redirect to NextJS with token
    async redirectToNextJS(targetPage = '') {
        try {
            // Show loading indicator
            console.log('Getting user data...');

            // Get user data from IndexedDB
            const userData = await this.getUserFromIndexedDB();
            console.log('User data retrieved:', userData);

            // Generate bridge token
            console.log('Generating bridge token...');
            const token = await this.generateBridgeToken(userData);
            console.log('Bridge token generated successfully');

            // Construct the URL with token
            const url = new URL(this.nextjsUrl + (targetPage.startsWith('/') ? '' : '/') + targetPage); 
            
            url.searchParams.set('token', token);

            // Redirect to NextJS app
            console.log('Redirecting to:', url.toString());
            window.location.href = url.toString();

        } catch (error) {
            console.error('Bridge redirect failed:', error);

            if (typeof alerta === 'function') {
                alerta('Error al conectar con la nueva aplicación. Por favor, inténtalo de nuevo.');
            } else {
                alert('Error al conectar con la nueva aplicación. Por favor, inténtalo de nuevo.');
            }
        }
    }

    // Test function to check backend connectivity
    async testBackend() {
        try {
            const response = await fetch(this.backendUrl, {
                method: 'GET'
            });

            const responseText = await response.text();
            console.log('Backend test response:', responseText);
            return responseText;
        } catch (error) {
            console.error('Backend test failed:', error);
            return null;
        }
    }
}

// Create global instance
window.userBridge = new UserBridge();

// Helper function for easy use in links/buttons
function redirectToNextJS(targetPage = '') {
    window.userBridge.redirectToNextJS(targetPage);
}

// Test function to debug backend issues
function testBackend() {
    window.userBridge.testBackend();
}
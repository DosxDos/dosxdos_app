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

    // Store user data in IndexedDB (for when returning from NextJS)
    async storeUserInIndexedDB(userData) {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName);

            request.onerror = () => reject(request.error);

            request.onsuccess = () => {
                const db = request.result;
                const transaction = db.transaction([this.storeName], 'readwrite');
                const store = transaction.objectStore(this.storeName);

                // Clear existing data and add new user
                store.clear();
                const addRequest = store.add(userData);

                addRequest.onsuccess = () => {
                    console.log('User data stored in IndexedDB successfully');
                    resolve(userData);
                };
                addRequest.onerror = () => reject(addRequest.error);
            };
        });
    }

    // NEW: Clear user data from IndexedDB (for logout)
    async clearUserFromIndexedDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName);
            
            request.onerror = () => reject(request.error);
            
            request.onsuccess = () => {
                const db = request.result;
                const transaction = db.transaction([this.storeName], 'readwrite');
                const store = transaction.objectStore(this.storeName);
                
                const clearRequest = store.clear();
                
                clearRequest.onsuccess = () => {
                    console.log('✅ User data cleared from IndexedDB');
                    resolve(true);
                };
                
                clearRequest.onerror = () => reject(clearRequest.error);
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

    // Decode token using backend (for when returning from NextJS)
    async decodeToken(token) {
        try {
            console.log('Decoding token via backend...');
            
            const response = await fetch(this.backendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'decode_token',
                    token: token
                })
            });

            if (!response.ok) {
                throw new Error('Token validation failed');
            }

            const result = await response.json();
            
            if (result.success) {
                console.log('Token decoded successfully:', result.user_data);
                return result.user_data;
            } else {
                throw new Error(result.error || 'Token decode failed');
            }

        } catch (error) {
            console.error('Token decode error:', error);
            return null;
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

    // Handle incoming token from NextJS when user returns to old app
    async handleIncomingBridgeToken() {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('bridge_token');
            
            if (token) {
                console.log('🔄 Incoming bridge token detected from NextJS, processing...');
                
                // Decode token to get user data
                const userData = await this.decodeToken(token);
                
                if (userData) {
                    // Store updated user data in IndexedDB
                    await this.storeUserInIndexedDB(userData);
                    console.log('✅ User data synchronized from NextJS app successfully');
                    
                    // Clean URL by removing token parameter
                    const cleanUrl = new URL(window.location);
                    cleanUrl.searchParams.delete('bridge_token');
                    window.history.replaceState({}, '', cleanUrl.pathname + cleanUrl.search);
                    
                    // Refresh the page to reload user data throughout the app
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                    
                    return true; // Token was processed successfully
                } else {
                    console.error('❌ Failed to decode token from NextJS');
                }
            }
            return false; // No token found or processing failed
        } catch (error) {
            console.error('❌ Error handling incoming bridge token:', error);
            return false;
        }
    }

    // NEW: Handle logout request from NextJS
    async handleLogoutFromNextJS() {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            
            if (action === 'logout') {
                console.log('🔄 Logout request from NextJS app detected...');
                
                // Clear IndexedDB user data
                await this.clearUserFromIndexedDB();
                
                // Clear any other session data you might have
                // Add any other cleanup you need here
                
                // Clean URL
                const cleanUrl = new URL(window.location);
                cleanUrl.searchParams.delete('action');
                window.history.replaceState({}, '', cleanUrl.pathname + cleanUrl.search);
                
                console.log('✅ Logout completed, user data cleared');
                
                // Optional: Show a logout message
                if (typeof alerta === 'function') {
                    alerta('Sesión cerrada correctamente');
                }
                
                // Refresh page to show logged out state
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
                return true;
            }
            return false;
        } catch (error) {
            console.error('❌ Error handling logout from NextJS:', error);
            return false;
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

// AUTO-INITIALIZE: Check for incoming tokens and logout requests when page loads
document.addEventListener('DOMContentLoaded', () => {
    console.log('🔧 Bridge initialized, checking for incoming tokens and logout requests...');
    
    // Check for logout first, then tokens
    window.userBridge.handleLogoutFromNextJS().then((logoutHandled) => {
        if (!logoutHandled) {
            // Only check for tokens if logout wasn't handled
            window.userBridge.handleIncomingBridgeToken();
        }
    });
});
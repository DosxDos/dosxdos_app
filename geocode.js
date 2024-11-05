// Function to fetch and geocode addresses
function fetchAndGeocode() {
  // Load the JSON data from the file
  fetch("/pdvs14.json")
    .then((response) => {
      return response.json();
    })
    .then((data) => {
      geocodeAddresses(data);
    })
    .catch((error) => {
      console.error("Error loading JSON data:", error);
    });
}

// Function to geocode addresses using Google Geocoding API
function geocodeAddresses(pinPoints) {
  const geocoder = new google.maps.Geocoder();
  let updatedPoints = [];
  let completedRequests = 0;

  pinPoints.forEach((point, index) => {
    // Concatenate the address with more details to improve geocoding accuracy
    const address = `${point.Dirección}, ${point.ZONA}, ${point["Código postal"]}, ${point["Área"]}, ${point["Nº"]}`;

    setTimeout(() => {
      geocoder.geocode({ address: address }, (results, status) => {
        if (status === "OK" && results[0]) {
          point.lat = results[0].geometry.location.lat();
          point.lng = results[0].geometry.location.lng();
        } else {
          console.error(`Geocode failed for ${address}: ${status}`);
          point.lat = null;
          point.lng = null;
        }

        updatedPoints.push(point);
        completedRequests++;

        // Check if all requests are completed
        if (completedRequests === pinPoints.length) {
          downloadUpdatedJSON(updatedPoints);
        }
      });
    }, index * 200); // Delay to avoid rate limits
  });
}

// Function to download the updated JSON file
function downloadUpdatedJSON(data) {
  const jsonStr = JSON.stringify(data, null, 2);
  const blob = new Blob([jsonStr], { type: "application/json" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "UPDATED_PDVS14.json";
  a.click();
  URL.revokeObjectURL(url);
  console.log("Updated JSON file downloaded successfully!");
}

fetchAndGeocode();

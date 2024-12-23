// Function to open the modal with options
function openModal() {
    document.getElementById('modal').style.display = 'block';
}

// Function to close the modal
function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

// Function to trigger file input (select from folder)
function selectFromFolder() {
    document.getElementById('photo').click(); // Trigger file input
    closeModal(); // Close the modal after selecting
}

// Function to start the webcam (capture from webcam)
function startWebcam() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            document.getElementById('webcam').style.display = 'block';
            document.getElementById('capture-btn').style.display = 'block';
            document.getElementById('webcam').srcObject = stream;
            closeModal(); // Close the modal after starting the webcam
        })
        .catch(err => {
            alert('Error accessing webcam: ' + err);
        });
}

// Function to capture the photo from the webcam
function capturePhoto() {
    const webcam = document.getElementById('webcam');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');

    // Draw the webcam image to the canvas
    context.drawImage(webcam, 0, 0, canvas.width, canvas.height);

    // Convert the canvas image to a data URL
    const dataUrl = canvas.toDataURL('image/png');

    // Set the captured image as the source for the image preview
    const photoPreview = document.getElementById('photo-preview');
    photoPreview.src = dataUrl;

    // Stop the webcam stream after capturing the photo
    const stream = webcam.srcObject;
    const tracks = stream.getTracks();
    tracks.forEach(track => track.stop());

    // Hide the webcam and capture button after capturing the image
    document.getElementById('webcam').style.display = 'none';
    document.getElementById('capture-btn').style.display = 'none';
}

// Function to preview the selected image from the file input
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photo-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
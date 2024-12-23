const startWebcamButton = document.getElementById('startWebcam');
    const webcam = document.getElementById('webcam');
    const captureButton = document.getElementById('capturePhoto');
    const photoInput = document.getElementById('photoInput');
    const webcamContainer = document.querySelector('.webcam-container');
    const capturedPhoto = document.getElementById('capturedPhoto');
    const photoDataInput = document.getElementById('photoData');
    let webcamStream = null;

    // Switch to webcam mode
    startWebcamButton.addEventListener('click', async () => {
        // Hide the file input and show the webcam
        photoInput.style.display = 'none';
        webcamContainer.style.display = 'block';

        try {
            // Access the webcam stream
            webcamStream = await navigator.mediaDevices.getUserMedia({ video: true });
            webcam.srcObject = webcamStream;
        } catch (error) {
            alert('Error accessing webcam: ' + error.message);
        }
    });

    // Capture photo from webcam
    captureButton.addEventListener('click', () => {
        if (webcamStream) {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = webcam.videoWidth;
            canvas.height = webcam.videoHeight;

            // Draw the current video frame onto the canvas
            context.drawImage(webcam, 0, 0, canvas.width, canvas.height);

            // Convert canvas to image data URL
            const photoData = canvas.toDataURL('image/png');

            // Update the hidden input with base64 data
            photoDataInput.value = photoData;

            // Display the captured photo
            capturedPhoto.src = photoData;
            capturedPhoto.style.display = 'block';
        }
    });

    // Show selected file as image preview
    photoInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                capturedPhoto.src = e.target.result;
                capturedPhoto.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
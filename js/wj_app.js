// $(() => {
//     // Initialize webcam
//     const video = document.getElementById('webcam');
//     const canvas = document.getElementById('canvas');
//     const captureButton = document.getElementById('captureButton');
//     const photoPreview = document.getElementById('photoPreview');

//     // Get webcam stream
//     navigator.mediaDevices.getUserMedia({ video: true })
//         .then((stream) => {
//             video.srcObject = stream;
//         })
//         .catch((error) => {
//             console.error("Error accessing webcam:", error);
//         });

//     // Capture the image from the webcam feed
//     captureButton.addEventListener('click', () => {
//         const context = canvas.getContext('2d');
//         context.drawImage(video, 0, 0, canvas.width, canvas.height);

//         // Get the image data URL (base64 encoded) and set it as the preview image
//         const dataURL = canvas.toDataURL('image/jpeg');

//         // Display the captured image in the preview and hold it
//         photoPreview.src = dataURL; // This sets the captured image to the img element with id "photoPreview"
        
//         // Disable the capture button to prevent re-capturing unless manually triggered
//         captureButton.disabled = true;

//         // Optional: You can add further functionality to save or upload the photo
//         console.log("Captured image URL:", dataURL); // For debugging purposes
//     });

//     // Handle file input (as in your original code)
//     $('label.upload input[type=file]').on('change', e => {
//         const f = e.target.files[0];
//         const img = $(e.target).siblings('img')[0];

//         if (!img) return;

//         img.dataset.src ??= img.src;

//         if (f?.type.startsWith('image/')) {
//             img.src = URL.createObjectURL(f);
//         } else {
//             img.src = img.dataset.src;
//             e.target.value = '';
//         }
//     });

//     // Optionally, allow user to reset and recapture the photo
//     captureButton.addEventListener('dblclick', () => {
//         // Reset the preview and enable the capture button
//         photoPreview.src = "/images/photo.jpg"; // Reset to initial image or placeholder
//         captureButton.disabled = false;
//     });
// });
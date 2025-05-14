const imageInput = document.getElementById('image-input');
const binaryInput = document.getElementById('image-binary');

imageInput.addEventListener('change', handleImageChange);

function handleImageChange() {
    const file = imageInput.files[0];

    if (file) {
        // Read the selected image file as a data URL
        const reader = new FileReader();
        reader.onload = () => {
            const dataUrl = reader.result;

            // Set the data URL as the source of the image
            binaryInput.value = dataUrl;
            preview.src = dataUrl;
        };

        reader.readAsDataURL(file);
    }
}
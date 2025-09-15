function getFileType(file) {
    if(file.type.match('image.*'))
        return 'image';
    if(file.type.match('video.*'))
        return 'video';
    return 'other';
}


function getImageDimensions(file) {
    return new Promise((resolve, reject) => {
        if (file && file.type.match('image.*')) {
            var reader = new FileReader();

            reader.onload = function(e) {
                var img = new Image();

                img.onload = function() {
                    var dimensions = [img.width, img.height];
                    resolve(dimensions);
                };

                img.onerror = function() {
                    reject(new Error('Failed to load image'));
                };

                img.src = e.target.result;
            };

            reader.onerror = function() {
                reject(new Error('Failed to read file'));
            };

            reader.readAsDataURL(file);
        } else {
            reject(new Error('Selected file is not an image'));
        }
    });
}

// Usage example
document.getElementById('fileInput').addEventListener('change', function(event) {
    var file = event.target.files[0];
    getImageDimensions(file)
        .then(dimensions => {
            var width = dimensions[0];
            var height = dimensions[1];
            console.log('File dimensions:', width, 'x', height);

            // Perform your own dimension checks here
            if (width === 150 && height === 150) {
                console.log('File dimensions are correct:', width, 'x', height);
                // File dimensions are correct, proceed with the form submission or other logic
            } else {
                console.log('File dimensions are incorrect:', width, 'x', height);
                alert('File dimensions must be 150x150 pixels.');
                // Clear the file input
                event.target.value = '';
            }
        })
        .catch(error => {
            console.log(error.message);
            alert(error.message);
            // Clear the file input
            event.target.value = '';
        });
});

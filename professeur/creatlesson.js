document.addEventListener('DOMContentLoaded', () => {
    const contentTypeButtons = document.querySelectorAll('.content-type-button');
    const videoUpload = document.getElementById('video-upload');
    const pdfUpload = document.getElementById('pdf-upload');
    const youtubeInput = document.getElementById('youtube-input');
    const durationGroup = document.getElementById('duration-group');
    const uploadButton = document.querySelector('.upload-button');
    const videoFileInput = document.getElementById('video-file');
    const fileUploadArea = document.querySelector('.file-upload-area');

    // Toggle content type sections
    function hideAll() {
        videoUpload.style.display = 'none';
        pdfUpload.style.display = 'none';
        youtubeInput.style.display = 'none';
        durationGroup.style.display = 'none'; // Hide duration by default
    }

    contentTypeButtons.forEach(button => {
        button.addEventListener('click', () => {
            contentTypeButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const type = button.dataset.type;
            hideAll();

            if (type === 'video') {
                videoUpload.style.display = 'block';
                durationGroup.style.display = 'block';
            } else if (type === 'pdf') {
                pdfUpload.style.display = 'block';
            } else if (type === 'youtube') {
                youtubeInput.style.display = 'block';
            }
        });
    });

    // Trigger file input and update button text
    if (uploadButton && videoFileInput) {
        uploadButton.addEventListener('click', () => {
            videoFileInput.click();
        });

        videoFileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                uploadButton.textContent = e.target.files[0].name;
            }
        });
    }

    // Drag and drop functionality
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = 'var(--primary-purple)';
    });

    fileUploadArea.addEventListener('dragleave', () => {
        fileUploadArea.style.borderColor = 'var(--border-color)';
    });

    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = 'var(--border-color)';
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            videoFileInput.files = files;
            uploadButton.textContent = files[0].name;
        }
    });

    // Set initial state based on active button
    const activeBtn = document.querySelector('.content-type-button.active');
    if (activeBtn) {
        activeBtn.click(); // Simulate click to set initial display
    }
});
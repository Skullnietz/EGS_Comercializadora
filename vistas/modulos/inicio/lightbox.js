$(document).ready(function() {
    // Event delegation for dynamically loaded content or existing content
    $('.users-list').on('click', 'img', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent the click from bubbling up to the anchor tag

        var imgSrc = $(this).attr('src');
        
        // Check if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                imageUrl: imgSrc,
                imageAlt: 'Orden Image',
                showCloseButton: true,
                showConfirmButton: false,
                background: 'transparent',
                backdrop: `
                    rgba(0,0,123,0.4)
                `,
                customClass: {
                    popup: 'animated fadeInDown',
                    image: 'img-fluid'
                }
            });
        } else {
            console.error('SweetAlert2 is not loaded');
            // Fallback or alert if Swal is missing
            alert('Image: ' + imgSrc);
        }
        
        return false; // Extra safety to prevent link navigation
    });

    // Prevent the anchor link wrapping the image from triggering
    $('.users-list').on('click', 'a:has(img)', function(e) {
        e.preventDefault();
    });
});

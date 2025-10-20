document.getElementById('addPostBtn').addEventListener('click', function () {
    document.getElementById('postOverlay').style.display = 'flex';
});

document.getElementById('cancelPost').addEventListener('click', function () {
    document.getElementById('postOverlay').style.display = 'none';
});

document.getElementById('postOverlay').addEventListener('click', function (e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});

let currentPostId = null;
function deletePost(postId) {
    const postElement = document.querySelector(`.post-container[data-id="${postId}"]`);
    const postUsername = postElement.getAttribute('data-username');
    const currentUser = window.currentUser;

    if (postUsername !== currentUser) {
        alert("You can only delete your own posts.");
        return;
    }

    currentPostId = postId;
    document.getElementById('deleteOverlay').style.display = 'flex';
}

document.getElementById('cancelDelete').addEventListener('click', function () {
    document.getElementById('deleteOverlay').style.display = 'none';
    currentPostId = null;
});

document.getElementById('confirmDelete').addEventListener('click', function () {
    if (currentPostId) {
        fetch(`delete-post.php?id=${currentPostId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => {
                // Cek apakah respons benar-benar JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    throw new Error('Response is not JSON. Check delete-post.php for errors.');
                }
            })
            .then(data => {
                if (data.success) {
                    const postElement = document.querySelector(`.post-container[data-id="${currentPostId}"]`);
                    if (postElement) postElement.remove();
                    document.getElementById('deleteOverlay').style.display = 'none';
                    currentPostId = null;
                    alert("Post deleted successfully!");
                } else {
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Deletion failed:', error);
                alert("Deletion failed: " + error.message);
            });
    }
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.more-options')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }

    const deleteBox = document.querySelector('#deleteOverlay .delete-confirm-box');
    if (e.target.id === 'deleteOverlay' && deleteBox && !deleteBox.contains(e.target)) {
        document.getElementById('deleteOverlay').style.display = 'none';
        currentPostId = null;
    }
});

function toggleMenu(postId) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-${postId}`) {
            menu.style.display = 'none';
        }
    });

    const menu = document.getElementById(`menu-${postId}`);
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

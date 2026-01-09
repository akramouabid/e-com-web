function toggleRole(userId, currentRole) {
    const newRole = currentRole === 'user' ? 'admin' : 'user';

    if (confirm(`Changer le rôle en ${newRole}?`)) {
        fetch('/src/api/update-user-role.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `user_id=${encodeURIComponent(userId)}&role=${encodeURIComponent(newRole)}`
        })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            console.log('data is: ', data);
            if (data.success) {
                location.reload();
            }
            else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Full Error:', error);
            alert("Check the Network Tab! The server returned something that isn't JSON.");
        });
    }
}

function deleteBook(bookId) {
    console.log('Book id to be deleted is: ', bookId);

    if (confirm('Êtes-vous sûr de vouloir supprimer ce livre?')) {
        fetch('/src/api/delete-book.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `book_id=${encodeURIComponent(bookId)}`
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log(`data is: ${data}`);
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error(error));
    }
}
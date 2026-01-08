let navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   searchForm.classList.remove('active');
   profile.classList.remove('active');
}

let profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   searchForm.classList.remove('active');
   navbar.classList.remove('active');
}

let searchForm = document.querySelector('.header .flex .search-form');

document.querySelector('#search-btn').onclick = () =>{
   searchForm.classList.toggle('active');
   navbar.classList.remove('active');
   profile.classList.remove('active');
}

window.onscroll = () =>{
   profile.classList.remove('active');
   navbar.classList.remove('active');
   searchForm.classList.remove('active');
}

document.querySelectorAll('.content-150').forEach(content => {
   if(content.innerHTML.length > 150) content.innerHTML = content.innerHTML.slice(0, 150);
});

function likePost(button, postId, adminId) {
   const formData = new FormData();
   formData.append('action', 'like_post');
   formData.append('post_id', postId);
   formData.append('admin_id', adminId);
   
   fetch('api/ajax_handler.php', {
      method: 'POST',
      body: formData
   })
   .then(response => response.json())
   .then(data => {
      if(data.success) {
         const icon = button.querySelector('i');
         const countSpan = button.querySelector('span');
         
         if(data.has_liked) {
            icon.style.color = 'var(--red)';
         } else {
            icon.style.color = '';
         }
         
         countSpan.textContent = '(' + data.likes_count + ')';
      } else if(data.redirect) {
         window.location.href = data.redirect;
      } else {
         alert(data.message);
      }
   })
   .catch(error => {
      console.error('Error:', error);
   });
   
   return false;
}

function submitComment(form) {
   const formData = new FormData(form);
   formData.append('action', 'add_comment');
   
   fetch('api/ajax_handler.php', {
      method: 'POST',
      body: formData
   })
   .then(response => response.json())
   .then(data => {
      if(data.success) {
         location.reload();
      } else if(data.redirect) {
         window.location.href = data.redirect;
      } else {
         alert(data.message);
      }
   })
   .catch(error => {
      console.error('Error:', error);
   });
   
   return false;
}

function deleteComment(commentId) {
   if(!confirm('Delete this comment?')) return false;
   
   const formData = new FormData();
   formData.append('action', 'delete_comment');
   formData.append('comment_id', commentId);
   
   fetch('api/ajax_handler.php', {
      method: 'POST',
      body: formData
   })
   .then(response => response.json())
   .then(data => {
      if(data.success) {
         location.reload();
      } else {
         alert(data.message);
      }
   })
   .catch(error => {
      console.error('Error:', error);
   });
   
   return false;
}

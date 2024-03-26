const slug = document.getElementById('slug');
const title = document.getElementById('title');
title.addEventListener('blur', () => {
    slug.value = title.value.trim().toLowerCase().split(' ').join('-');
})
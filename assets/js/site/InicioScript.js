// JavaScript source code
document.getElementById('navbutton').addEventListener('click', function () {
    const navbar = document.getElementById('mainNav');
    if (!(window.scrollY > 100)) {
        navbar.classList.toggle('scrolled');
    }
   
});
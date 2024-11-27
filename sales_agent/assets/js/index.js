const sideMenu = document.querySelector('aside');
const menuBtn = document.getElementById('menu-btn');
const closeBtn = document.getElementById('close-btn');

menuBtn.addEventListener('click', () => {
    sideMenu.style.display = 'block';
});

closeBtn.addEventListener('click', () => {
    sideMenu.style.display = 'none';
});


/* for orders to paltan siguro to
 ng dko alam static lang to e kinukuwa nya lang data sa order.js*/

/*Tinanggal ko yung order list d2 sa index at nalilito yung table*/

/*para sa sidebar bat clinick mahihiglight*/
document.addEventListener("DOMContentLoaded", function () {
    const sidebarLinks = document.querySelectorAll(".sidebar a");

    sidebarLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            // Remove active class from all links
            sidebarLinks.forEach((el) => el.classList.remove("active"));

            // Add active class to the clicked link
            link.classList.add("active");
        });
    });
});

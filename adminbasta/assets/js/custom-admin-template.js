const customTopBar = document.getElementById("TopNav")
const customSideBar = document.getElementById("sidebarNav")
const customFooter = document.getElementById("footer")
const NavBarBrand = document.getElementById("navBarBrand-container")
const NavBarMenu = document.getElementById("navBarMenu-container")
const MainWrapper = document.getElementById("main-wrapper")
const SideBarToggle = document.getElementById("SideBarToggle")
var SideBarwidth = customSideBar.clientWidth

window.cusotmAdminTemplate = function(){
    var sb_is_toggle = !!SideBarToggle.getAttribute('data-toggle') && SideBarToggle.getAttribute('data-toggle') == "true";
    if(sb_is_toggle){
        customSideBar.style.width = 0;
        customFooter.style.width = (window.innerWidth) + `px`;
        customFooter.style.marginLeft = `0px`;
        MainWrapper.style.width =(window.innerWidth) + `px`; 
        MainWrapper.style.marginLeft = `0px`;
        // For laptop, desktop, or bigger screens only
        if(window.innerWidth > 768)
            NavBarBrand.style.width = `auto`;
        SideBarToggle.style.display = 'block'; // Show toggle button
    }else{
        if(window.innerWidth > 768){
            // For laptop, desktop, or bigger screens only
            if(customSideBar.clientWidth == 0)
                customSideBar.style.width = SideBarwidth + `px`;
            customSideBar.style.height = (window.innerHeight -  customTopBar.clientHeight) + `px`;
            customSideBar.style.marginTop = (customTopBar.clientHeight) + `px`;
            customFooter.style.width = (window.innerWidth -  SideBarwidth) + `px`;
            customFooter.style.marginLeft = (SideBarwidth) + `px`;
            NavBarBrand.style.width = SideBarwidth + `px`;
            NavBarMenu.style.width =(window.innerWidth -  SideBarwidth) + `px`; 
            MainWrapper.style.width =(window.innerWidth -  SideBarwidth) + `px`; 
            MainWrapper.style.marginLeft = (SideBarwidth) + `px`;
            SideBarToggle.style.display = 'none'; // Hide toggle button
        }else{
            // For mobile, tablets, or smaller screens only
            if(customSideBar.clientWidth == 0)
                customSideBar.style.width = SideBarwidth + `px`;
            customFooter.style.width = (window.innerWidth) + `px`;
            customFooter.style.marginLeft = `0px`;
            MainWrapper.style.width =(window.innerWidth) + `px`; 
            MainWrapper.style.marginLeft = `0px`;
            customSideBar.style.height = (window.innerHeight -  customTopBar.clientHeight) + `px`;
            customSideBar.style.marginTop = (customTopBar.clientHeight) + `px`;
            SideBarToggle.style.display = 'block'; // Show toggle button
        }
    }
}

cusotmAdminTemplate();

window.onload = function(){
    cusotmAdminTemplate();
    // hide sidebar as default for smaller screens
    if(window.innerWidth <= 768){
        SideBarToggle.setAttribute('data-toggle', "true")
        customSideBar.style.width = `0px`;
    }
    // enable the sidebar visibility
    setTimeout(function(){
        customSideBar.style.opacity = '1'
    }, 300)
}

/** Update containers sizes when resizing the window screen */
window.onresize = function(){
    cusotmAdminTemplate();
}

/**
 * Toggle SideBar
 */
SideBarToggle.addEventListener('click', function(){
    if(!!SideBarToggle.getAttribute('data-toggle') && SideBarToggle.getAttribute('data-toggle') == "true"){
        SideBarToggle.setAttribute('data-toggle', "false")
    }else{
        SideBarToggle.setAttribute('data-toggle', "true")
    }
    cusotmAdminTemplate();
})

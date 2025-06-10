/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function openDropdown(dropdown) {
    const dropdowns = document.getElementsByClassName("dropdown-content");
    let i;
    for (i = 0; i < dropdowns.length; i++) {
        const openDropdown = dropdowns[i];
        if (dropdowns[i] !== document.getElementById(dropdown)) {
            openDropdown.classList.remove("show");
        }
    }
    let dropdown_content = document.getElementById(dropdown);
    dropdown_content.classList.toggle("show");
}

// Close the dropdown if the user clicks in window.
window.onclick = function (event) {
    if (!event.target.matches(".dropbtn")) {
        const dropdowns = document.getElementsByClassName("dropdown-content");
        let i;
        for (i = 0; i < dropdowns.length; i++) {
            const openDropdown = dropdowns[i];
            if (openDropdown.classList.contains("show")) {
                openDropdown.classList.remove("show");
            }
        }
    }
}
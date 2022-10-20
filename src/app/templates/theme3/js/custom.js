$(function() {
    // close side menu on small devices
    $('#side-menu a[generator="adianti"]').click(function() {
        $('body').removeClass('sidebar-open');
        $('body').scrollTop(0);
    })
    
    setTimeout( function() {
        $('#envelope_messages a').click(function() { $(this).closest('.dropdown.open').removeClass('open'); });
        $('#envelope_notifications a').click(function() { $(this).closest('.dropdown.open').removeClass('open'); });
    }, 500);
});

function searchMenu() {
    let inputSearch = document.querySelector('#search-menu');
    let listMenu = document.querySelectorAll('#side-menu li');
    inputSearch.addEventListener('keyup', () => {
        let filterText = inputSearch.value.trim();
        let matchElements = [];

        listMenu.forEach(item => {
            let toggle = item;
            let toggleMenu = item.querySelector('ul.treeview-menu');

            if (filterText === '') {
                item.classList.remove('d-none');
                if (toggle) {
                    toggle.classList.remove('active');
                }

                if (toggleMenu) {
                    toggleMenu.style.display = 'none';
                }
            } else if (item.textContent.search(new RegExp(filterText, "i")) < 0) {
                item.classList.add('d-none');
            } else {
                matchElements.push(item.querySelector('a span'));
                item.classList.remove('d-none');
                if (toggle) {
                    toggle.classList.add('active');
                }

                if (toggleMenu) {
                    toggleMenu.style.display = 'block';
                }
            }
        });

        // abre o menu / submenu inteiro se o match for apenas com o li pai
        matchElements = [...new Set(matchElements)];
        matchElements = matchElements.filter(elem => {
            return elem.textContent.search(new RegExp(filterText, "i")) > -1;
        });
        matchElements.forEach(item => {
            let lists = item.parentElement.parentElement.querySelectorAll('ul.treeview-menu li');
            lists.forEach(list => {
                list.classList.remove('d-none');
            });
        });
    });
}

window.addEventListener('DOMContentLoaded', (event) => {
    searchMenu();
});

$( document ).on( 'click', 'ul.dropdown-menu a[generator="adianti"]', function() {
    $(this).parents(".dropdown.show").removeClass("show");
    $(this).parents(".dropdown-menu.show").removeClass("show");
});
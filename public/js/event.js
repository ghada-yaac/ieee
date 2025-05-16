document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const eventItems = document.querySelectorAll('.event-item');
    const mainSearchInput = document.querySelector('.events-search-bar input');
    const sidebarSearchInput = document.querySelector('.sidebar-widget .search-box input');
    const searchButtons = document.querySelectorAll('button i.fa-search');
    const categoryFilters = document.querySelectorAll('.sidebar-widget:nth-child(2) .filter-options input[type="checkbox"]');
    const locationFilters = document.querySelectorAll('.sidebar-widget:nth-child(4) .filter-options input[type="checkbox"]');
    const dateFilters = document.getElementById('start-date');
    const endDateFilters = document.getElementById('end-date');
    const applyFilterBtn = document.querySelector('.apply-filter');
    const sortSelect = document.getElementById('sort-events');
    const eventsCountSpan = document.querySelector('.events-count span');

    // Fonction principale de filtrage
    function filterEvents() {
        const mainSearchTerm = mainSearchInput.value.toLowerCase();
        const sidebarSearchTerm = sidebarSearchInput.value.toLowerCase();
        const startDate = dateFilters.value ? new Date(dateFilters.value) : null;
        const endDate = endDateFilters.value ? new Date(endDateFilters.value) : null;

        // Get selected categories
        const selectedCategories = [];
        document.querySelectorAll('.sidebar-widget:nth-child(2) .filter-options input[type="checkbox"]:checked').forEach(checkbox => {
            const labelText = checkbox.parentElement.querySelector('span:last-child').textContent;
            if (labelText !== 'All Categories') {
                selectedCategories.push(labelText.toLowerCase());
            }
        });

        // Get selected locations
        const selectedLocations = [];
        document.querySelectorAll('.sidebar-widget:nth-child(4) .filter-options input[type="checkbox"]:checked').forEach(checkbox => {
            const labelText = checkbox.parentElement.querySelector('span:last-child').textContent;
            if (labelText !== 'All Locations') {
                selectedLocations.push(labelText.toLowerCase());
            }
        });

        let visibleCount = 0;

        eventItems.forEach(item => {
            const eventName = item.querySelector('h3').textContent.toLowerCase();
            const eventDescription = item.querySelector('.event-description p').textContent.toLowerCase();
            const eventCategory = item.getAttribute('data-category');
            const eventDate = new Date(item.getAttribute('data-date'));
            const eventLocation = item.querySelector('.event-meta span:nth-child(2)').textContent.toLowerCase();

            // Vérifier les critères de recherche (les deux champs de recherche)
            const matchesMainSearch = mainSearchTerm === '' ||
                eventName.includes(mainSearchTerm) ||
                eventDescription.includes(mainSearchTerm);

            const matchesSidebarSearch = sidebarSearchTerm === '' ||
                eventName.includes(sidebarSearchTerm) ||
                eventDescription.includes(sidebarSearchTerm);

            // Vérifier les catégories sélectionnées
            const matchesCategory = selectedCategories.length === 0 ||
                selectedCategories.includes(eventCategory);

            // Vérifier la plage de dates
            let matchesDate = true;
            if (startDate && endDate) {
                matchesDate = eventDate >= startDate && eventDate <= endDate;
            } else if (startDate) {
                matchesDate = eventDate >= startDate;
            } else if (endDate) {
                matchesDate = eventDate <= endDate;
            }

            // Vérifier la localisation
            let matchesLocation = selectedLocations.length === 0 ||
                selectedLocations.some(loc => eventLocation.includes(loc));

            // Afficher ou masquer l'élément en fonction des critères
            if (matchesMainSearch && matchesSidebarSearch && matchesCategory && matchesDate && matchesLocation) {
                item.closest('.event-card').style.display = '';
                visibleCount++;
            } else {
                item.closest('.event-card').style.display = 'none';
            }
        });

        // Mettre à jour le compteur d'événements
        eventsCountSpan.textContent = visibleCount;

        // Afficher un message si aucun événement n'est trouvé
        const noEventsDiv = document.querySelector('.no-events');
        if (visibleCount === 0 && eventItems.length > 0) {
            if (!noEventsDiv) {
                const eventsGrid = document.querySelector('.events-grid');
                const noEventsHTML = `
                    <div class="no-events">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No events found</h3>
                        <p>There are currently no events matching your criteria. Please try different filters.</p>
                    </div>
                `;
                eventsGrid.insertAdjacentHTML('beforeend', noEventsHTML);
            }
        } else if (noEventsDiv) {
            noEventsDiv.remove();
        }
    }

    // Fonction de tri des événements
    function sortEvents() {
        const sortValue = sortSelect.value;
        const eventsGrid = document.querySelector('.events-grid');
        const eventCards = Array.from(document.querySelectorAll('.event-card'));

        eventCards.sort((a, b) => {
            const aItem = a.querySelector('.event-item');
            const bItem = b.querySelector('.event-item');

            if (sortValue === 'date-asc') {
                const aDate = new Date(aItem.getAttribute('data-date'));
                const bDate = new Date(bItem.getAttribute('data-date'));
                return aDate - bDate;
            } else if (sortValue === 'date-desc') {
                const aDate = new Date(aItem.getAttribute('data-date'));
                const bDate = new Date(bItem.getAttribute('data-date'));
                return bDate - aDate;
            } else if (sortValue === 'name-asc') {
                const aName = aItem.querySelector('h3').textContent.toLowerCase();
                const bName = bItem.querySelector('h3').textContent.toLowerCase();
                return aName.localeCompare(bName);
            }
            return 0;
        });

        // Réorganiser les cartes dans le DOM
        eventCards.forEach(card => eventsGrid.appendChild(card));
    }

    // Fonction pour gérer les cases "All Categories" et "All Locations"
    function handleAllCheckboxes(clickedCheckbox, groupSelector, allText) {
        if (clickedCheckbox.parentElement.querySelector('span:last-child').textContent === allText && clickedCheckbox.checked) {
            // Décocher toutes les autres cases du même groupe
            document.querySelectorAll(groupSelector).forEach(cb => {
                if (cb !== clickedCheckbox && cb.parentElement.querySelector('span:last-child').textContent !== allText) {
                    cb.checked = false;
                }
            });
        } else if (clickedCheckbox.checked) {
            // Décocher "All Categories" ou "All Locations" si une autre case est cochée
            const allCheckbox = document.querySelector(`${groupSelector} + span:last-child`);
            if (allCheckbox && allCheckbox.textContent === allText) {
                allCheckbox.previousElementSibling.checked = false;
            }
        }
    }

    // Écouteurs d'événements
    mainSearchInput.addEventListener('input', filterEvents);
    sidebarSearchInput.addEventListener('input', filterEvents);

    searchButtons.forEach(button => {
        button.addEventListener('click', filterEvents);
    });

    categoryFilters.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            handleAllCheckboxes(this, '.sidebar-widget:nth-child(2) .filter-options input[type="checkbox"]', 'All Categories');
            filterEvents();
        });
    });

    locationFilters.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            handleAllCheckboxes(this, '.sidebar-widget:nth-child(4) .filter-options input[type="checkbox"]', 'All Locations');
            filterEvents();
        });
    });

    applyFilterBtn.addEventListener('click', filterEvents);
    sortSelect.addEventListener('change', sortEvents);

    // Initialisation
    filterEvents();
});
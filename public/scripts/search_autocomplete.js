document.addEventListener("DOMContentLoaded", () => {
    const inputStart =
        document.getElementById("search_carpool_startPlace") ||
        document.getElementById("carpool_start_place");

    const inputEnd =
        document.getElementById("search_carpool_endPlace") ||
        document.getElementById("carpool_end_place");

    if (!inputStart || !inputEnd) {
        console.error("Start or End input not found.");
        return;
    }

    const setupAutocomplete = (inputElement) => {
        const awesomplete = new Awesomplete(inputElement, {
            minChars: 2,
            maxItems: 10,
        });

        let debounceTimer = null;
        let lastQuery = "";

        const fetchCities = async (query) => {
            try {
                const response = await fetch(
                    "/search/carpool/api/cities?q=" + encodeURIComponent(query)
                );
                if (!response.ok) throw new Error("Network response was not ok");

                const cities = await response.json();

                if (query === inputElement.value.trim()) {
                    awesomplete.list = cities;
                }
            } catch (error) {
                console.error("Autocomplete error:", error);
            }
        };

        inputElement.addEventListener("input", () => {
            const query = inputElement.value.trim();
            if (query.length < 2 || query === lastQuery) return;
            lastQuery = query;

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchCities(query), 250);
        });

        inputElement.addEventListener("focus", () => {
            const query = inputElement.value.trim();
            if (query.length >= 2) fetchCities(query);
        });
    };

    setupAutocomplete(inputStart);
    setupAutocomplete(inputEnd);
});
document.addEventListener("awesomplete-selectcomplete", function (e) {
    e.target.dispatchEvent(new Event("change", { bubbles: true }));
});

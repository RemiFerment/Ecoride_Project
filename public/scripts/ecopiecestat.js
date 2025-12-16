fetch('/admin/api/admin/stats/ecopieces-per-day').then(res => {
    if (res.status === 404) {
        new Chart(document.getElementById('ecopieceChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Nombre d\'Écopièces',
                        data: [],
                        borderWidth: 2
                    }
                ]
            }
        });
        return;
    }
    return res.json();
}).then(data => {
    if (!data)
        return;

    const labels = data.map(d => d._id);
    const values = data.map(d => d.count);

    new Chart(document.getElementById('ecopieceChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Nombre d\'Écopièces',
                    data: values,
                    borderWidth: 2
                }
            ]
        }
    });
});
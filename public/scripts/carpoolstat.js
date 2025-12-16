fetch('/admin/api/admin/stats/carpools-per-day').then(res => {
    if (res.status === 404) {
        new Chart(document.getElementById('carpoolChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Nombre de covoiturages',
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

    new Chart(document.getElementById('carpoolChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Nombre de covoiturages',
                    data: values,
                    borderWidth: 2
                }
            ]
        }
    });
});
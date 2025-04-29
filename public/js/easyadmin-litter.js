document.addEventListener('DOMContentLoaded', () => {
    const breedSelect = document.querySelector('select[name="Litter[breed]"]');
    const motherSelect = document.querySelector('select[name="Litter[catMother]"]');
    const fatherSelect = document.querySelector('select[name="Litter[catFather]"]');
    const catsJsonField = document.getElementById('cats-json-data');

    if (!breedSelect || !motherSelect || !fatherSelect || !catsJsonField) return;

    const cats = JSON.parse(catsJsonField.value);

    const updateCatSelect = (select, gender) => {
        const breedId = parseInt(breedSelect.value);
        const filteredCats = cats.filter(cat => cat.breed === breedId && cat.gender === gender);

        // Очистка
        select.innerHTML = '';

        // Добавим пустой элемент
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = '-- выберите --';
        select.appendChild(emptyOption);

        // Добавим подходящих котов
        filteredCats.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            select.appendChild(option);
        });
    };

    breedSelect.addEventListener('change', () => {
        updateCatSelect(motherSelect, 2); // Самки
        updateCatSelect(fatherSelect, 1); // Самцы
    });

    // Инициализация при загрузке
    if (breedSelect.value) {
        updateCatSelect(motherSelect, 2);
        updateCatSelect(fatherSelect, 1);
    }
});

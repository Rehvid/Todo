/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/global.scss';

const submitFormFilterRoles = () => {

    const selectValue = document.querySelector('#js-user-roles');

    if (selectValue) {
        selectValue.addEventListener('change', () => {
            document.querySelector('#js-user-filter').submit()
        })
    }

}

const submitFormFilterPagination = () => {
    const selectValue = document.querySelector('#js-user-pagination');

    if (selectValue) {
        selectValue.addEventListener('change', () => {
            document.querySelector('#js-user-filter').submit()
        })
    }

}

const submitSelectFilters = () => {
    const formFilter = document.querySelector('#js-filter');

    if (formFilter) {
        const selects = formFilter.querySelectorAll('select') ?? [];
        selects.forEach(select => {
            select.addEventListener('change', () => {
                formFilter.submit();
            })
        })

    }

}

document.addEventListener('DOMContentLoaded', () => {
    submitFormFilterRoles();
    submitFormFilterPagination();
    submitSelectFilters();
})
import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

const form_filters = document.getElementById('filters');
const query = form_filters.querySelector('input[name="query"]');

// Auto submit la recherche des sorties, avec un délai pour la frappe
if (query) {
    let time;
    query.addEventListener('input', () => {
        clearTimeout(time);
        time = setTimeout(() => form_filters.requestSubmit(), 350);
    })
}
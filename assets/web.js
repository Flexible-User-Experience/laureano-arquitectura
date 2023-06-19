import './styles/web.scss';
import './stimulus_bootstrap';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
[...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

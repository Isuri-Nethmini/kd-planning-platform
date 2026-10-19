import './bootstrap';

import Alpine from 'alpinejs';
import { initTilt } from './tilt';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', initTilt);

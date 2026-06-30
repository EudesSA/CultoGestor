/*!
 * CultoGestor — Site público institucional (JavaScript)
 * Carrega o Alpine.js para as interações simples do site (menu mobile, accordion do FAQ).
 * Isolado do painel Filament — o Alpine do Filament só existe nas páginas do /admin.
 *
 * @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
 * @link   https://www.proezatech.com
 */

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();

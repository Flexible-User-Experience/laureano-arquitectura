import { Controller } from '@hotwired/stimulus';
import { Chart } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

export default class extends Controller
{
    connect()
    {
        this.element.addEventListener('chartjs:pre-connect', this._onPreConnect);
    }

    disconnect()
    {
        this.element.removeEventListener('chartjs:pre-connect', this._onPreConnect);
    }

    _onPreConnect(event)
    {
        Chart.register(ChartDataLabels);
        event.detail.options.plugins.datalabels.formatter = function(value) {
            return value + ' %';
        };
    }
}

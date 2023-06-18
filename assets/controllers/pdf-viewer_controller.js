import { Controller } from '@hotwired/stimulus';
import { getDocument, GlobalWorkerOptions } from 'pdfjs-dist/build/pdf';
GlobalWorkerOptions.workerSrc = require('pdfjs-dist/build/pdf.worker.entry');

export default class extends Controller
{
    static targets = [
        'loader',
        'warning',
        'canvas',
        'downloader',
        'current',
        'total',
        'pager',
    ];

    static values = {
        path: String,
        mime: String,
    }

    initialize()
    {
        this.pdfDoc = null;
        this.pdfPageNum = 1;
        this.pdfPageRendering = false;
        this.pdfPageNumPending = null;
        this.pdfScale = 1.0;
        this.pdfCtx = this.canvasTarget.getContext('2d');
    }

    connect()
    {
        if (this.mimeValue === 'application/pdf' && this.pathValue !== '') {
            let loadingTask = getDocument(this.pathValue);
            loadingTask.promise.then((pdf) => {
                this.pdfDoc = pdf;
                this.renderPage(1);
            }, (errorGet) => {
                console.error('Error during ' + this.pathValue + ' loading document:', errorGet);
                this.canvasTarget.classList.add('hide');
                this.loaderTarget.classList.add('hide');
                this.downloaderTarget.classList.add('hide');
                this.pagerTarget.classList.add('hide');
                this.warningTarget.classList.remove('hide');
            });
        } else {
            this.canvasTarget.classList.add('hide')
            this.loaderTarget.classList.add('hide');
            this.downloaderTarget.classList.add('hide');
            this.pagerTarget.classList.add('hide');
        }
    }

    renderPage(num)
    {
        this.pdfPageRendering = true;
        let self = this;
        this.pdfDoc.getPage(num).then(function(page) {
            let viewport = page.getViewport({ scale: self.pdfScale });
            self.canvasTarget.height = viewport.height;
            self.canvasTarget.width = viewport.width;
            let renderContext = {
                canvasContext: self.pdfCtx,
                viewport: viewport,
            };
            let renderTask = page.render(renderContext);
            renderTask.promise.then(function () {
                self.pdfPageRendering = false;
                if (self.pdfPageNumPending !== null) {
                    renderPage(self.pdfPageNumPending);
                    self.pdfPageNumPending = null;
                }
            });
            self.canvasTarget.classList.remove('hide');
            self.downloaderTarget.classList.remove('hide');
            self.loaderTarget.classList.add('hide');
        }, (errorGet) => {
            console.error('Error during ' + this.pathValue + ' loading first page:', errorGet);
            self.canvasTarget.classList.add('hide');
            self.downloaderTarget.classList.add('hide');
            self.loaderTarget.classList.add('hide');
            self.pagerTarget.classList.add('hide');
            self.warningTarget.classList.remove('hide');
        });
        this.currentTarget.textContent = num;
        this.totalTarget.textContent = this.pdfDoc.numPages;
        if (this.pdfDoc.numPages > 1) {
            self.pagerTarget.classList.remove('hide');
        }
    }

    queueRenderPage(num)
    {
        if (this.pdfPageRendering) {
            this.pdfPageNumPending = num;
        } else {
            this.renderPage(num);
        }
    }

    onPrevPage()
    {
        if (this.pdfPageNum <= 1) {
            return;
        }
        this.pdfPageNum--;
        this.queueRenderPage(this.pdfPageNum);
    }

    onNextPage()
    {
        if (this.pdfPageNum >= this.pdfDoc.numPages) {
            return;
        }
        this.pdfPageNum++;
        this.queueRenderPage(this.pdfPageNum);
    }

    update(event)
    {
        this.downloaderTarget.href = event.detail.path;
        this.loaderTarget.classList.add(this.hiddenClass);
        this.canvasTarget.classList.remove(this.hiddenClass);
        let loadingTask = getDocument(event.detail.path);
        loadingTask.promise.then((pdf) => {
            this.pdfDoc = pdf;
            this.renderPage(this.pdfPageNum);
        }, (errorGet) => {
            console.error('Error during ' + event.detail.path + ' loading document:', errorGet);
        });
    }
}

<?php

namespace App\Pdf\Builder;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Manager\AssetsManager;
use Qipsius\TCPDFBundle\Controller\TCPDFController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

class InvoicePdfBuilder
{
    private TCPDFController $tcpdf;
    private TranslatorInterface $ts;
    private ParameterBagInterface $pb;
    private MoneyFormatter $mf;
    private AssetsManager $am;
    private bool $isDebug;

    public function __construct(TCPDFController $tcpdf, TranslatorInterface $ts, ParameterBagInterface $pb, MoneyFormatter $mf, AssetsManager $am)
    {
        $this->tcpdf = $tcpdf;
        $this->ts = $ts;
        $this->pb = $pb;
        $this->mf = $mf;
        $this->am = $am;
        $this->isDebug = $this->pb->get('kernel.debug');
    }

    /**
     * @throws \ReflectionException
     */
    public function build(Invoice $invoice): \TCPDF
    {
        $customer = $invoice->getCustomer();
        $this->ts->setLocale($customer->getLocale());
        /** @var AppTcpdf $pdf */
        $pdf = $this->tcpdf->create($this->pb, $this->am);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($this->pb->get('project_web_title'));
        $pdf->SetTitle($this->ts->trans('Invoice').' '.$invoice->getSluggedSerialNumber());
        $pdf->SetSubject($this->ts->trans('Invoice').' '.$invoice->getSluggedSerialNumber());
        $pdf->setPrintHeader();
        $pdf->setPrintFooter();
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $pdf->SetMargins(AppTcpdf::PDF_MARGIN_LEFT, AppTcpdf::PDF_MARGIN_TOP, AppTcpdf::PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(true, AppTcpdf::PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // add start page
        $pdf->AddPage(PDF_PAGE_ORIENTATION, PDF_PAGE_FORMAT, true);

        // invoice header section
        $pdf->Ln(2);
        $pdf->usePrimaryFont();
        $pdf->setCellPaddings(1, 2, 1, 2);
        $pdf->setFillColorArray([255, 255, 255]);
        $pdf->Cell(AppTcpdf::AVAILABLE_WIDTH, 0, $this->ts->trans('Invoice').' '.$this->ts->trans('Number Short').' '.$invoice->getSluggedSerialNumber(), $this->isDebug, 1);
        $pdf->Cell(AppTcpdf::AVAILABLE_WIDTH, 0, $this->ts->trans('Date').' '.$invoice->getDateString(), $this->isDebug, 1);
        if ($invoice->getCustomerReference()) {
            $pdf->Cell(AppTcpdf::AVAILABLE_WIDTH, 0, $this->ts->trans('Customer Reference Short').' '.$invoice->getCustomerReference(), $this->isDebug, 1);
        }
        $pdf->Ln(AppTcpdf::GAP_Y);

        // provider & customer section
        $pdf->usePrimaryFont(13);
        $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $this->ts->trans('Provider Data'), $this->isDebug);
        $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $this->ts->trans('Customer Data'), $this->isDebug, 1);
        $pdf->drawInvoiceLineSeparator();
        $pdf->useNormalFont();
        $pdf->Ln(AppTcpdf::GAP_Y / 3);
        $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $this->pb->get('enterprise_name'), $this->isDebug, 0, 'L', true);
        $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $customer->getFiscalName(), $this->isDebug, 1, 'L', true);
        $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $this->pb->get('enterprise_tin'), $this->isDebug, 0, 'L', true);
        $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $customer->getFiscalIdentificationCode(), $this->isDebug, 1, 'L', true);
        $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $this->pb->get('enterprise_address'), $this->isDebug, 0, 'L', true);
        $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $customer->getAddress1(), $this->isDebug, 1, 'L', true);
        if ($customer->getAddress2()) {
            $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $this->pb->get('enterprise_zip').' '.$this->pb->get('enterprise_city').' ('.$this->pb->get('enterprise_state').')', $this->isDebug, 0, 'L', true);
            $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $customer->getAddress2(), $this->isDebug, 1, 'L', true);
            $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, '', $this->isDebug, 0, 'L', true);
            $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $customer->getOneLineLocation(), $this->isDebug, 1, 'L', true);
        // TODO render customer country
        } else {
            $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $this->pb->get('enterprise_zip').' '.$this->pb->get('enterprise_city').' ('.$this->pb->get('enterprise_state').')', $this->isDebug, 0, 'L', true);
            $pdf->Cell(AppTcpdf::HALF_AVAILABLE_WIDTH, 0, $customer->getOneLineLocation(), $this->isDebug, 1, 'L', true);
            // TODO render customer country
        }
        $pdf->Ln(AppTcpdf::GAP_Y);

        // invoice lines header
        $pdf->usePrimaryFont(13);
        $pdf->Cell(AppTcpdf::AVAILABLE_WIDTH, 0, $this->ts->trans('Invoice Lines Data'), $this->isDebug, 1);
        $pdf->drawInvoiceLineSeparator();
        $pdf->usePrimaryFont(8);
        $pdf->Ln(AppTcpdf::GAP_Y / 3);
        $colLg = 109;
        $colMd = 25;
        $colSm = 21;
        $colXs = 15;
        $pdf->Cell($colLg, 0, $this->ts->trans('Concept'), $this->isDebug);
        $pdf->Cell($colXs, 0, $this->ts->trans('Units'), $this->isDebug, 0, 'R');
        $pdf->Cell($colSm, 0, $this->ts->trans('Unit Price Short'), $this->isDebug, 0, 'R');
        $pdf->Cell($colMd, 0, $this->ts->trans('Amount'), $this->isDebug, 1, 'R');

        // invoice lines table
        $pdf->useNormalFont();
        /** @var InvoiceLine $line */
        foreach ($invoice->getInvoiceLines() as $line) {
            // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T')
            $pdf->MultiCell($colLg, 0, $line->getDescription(), $this->isDebug, 'L', true, 0, '', '', true, 0, false, true, AppTcpdf::GAP_Y + 5, 'M');
            $pdf->MultiCell($colXs, 0, $line->getUnitsString(), $this->isDebug, 'R', true, 0, '', '', true, 0, false, true, AppTcpdf::GAP_Y + 5, 'M');
            $pdf->MultiCell($colSm, 0, $this->mf->localizedFormatMoney($line->getUnitPrice()), $this->isDebug, 'R', true, 0, '', '', true, 0, false, true, AppTcpdf::GAP_Y + 5, 'M');
            $pdf->MultiCell($colMd, 0, $this->mf->localizedFormatMoney($line->getTotal()), $this->isDebug, 'R', true, 1, '', '', true, 0, false, true, AppTcpdf::GAP_Y + 5, 'M');
        }
        $pdf->Ln(AppTcpdf::GAP_Y / 3);

        // invoice lines footer
        $pdf->drawInvoiceLineSeparator();
        $pdf->Cell($colLg + $colSm + $colXs, 0, $this->ts->trans('Total Base'), $this->isDebug, 0, 'R', true);
        $pdf->Cell($colMd, 1, $this->mf->localizedFormatMoney($invoice->calculateBaseAmount()), $this->isDebug, 1, 'R', true);
        if ($invoice->getDiscountPercentage()) {
            $pdf->Cell($colLg + $colSm + $colXs, 0, $this->ts->trans('Discount').' '.$invoice->getDiscountPercentage().'%', $this->isDebug, 0, 'R', true);
            $pdf->Cell($colMd, 1, '-'.$this->mf->localizedFormatMoney($invoice->calculateDiscountBaseAmount()), $this->isDebug, 1, 'R', true);
        }
        $pdf->Cell($colLg + $colSm + $colXs, 0, $invoice->getTaxPercentage().'% '.$this->ts->trans('Tax Percentage Short'), $this->isDebug, 0, 'R', true);
        $pdf->Cell($colMd, 1, '+'.$this->mf->localizedFormatMoney($invoice->calculateTaxBaseAmount()), $this->isDebug, 1, 'R', true);

        // total invoice
        $pdf->drawInvoiceLineSeparator();
        $pdf->useBoldFont();
        $pdf->Cell($colLg + $colSm + $colXs, 0, strtoupper($this->ts->trans('Total')), $this->isDebug, 0, 'R', true);
        $pdf->Cell($colMd, 1, $this->mf->localizedFormatMoney($invoice->getTotal()), $this->isDebug, 1, 'R', true);
        $pdf->Ln(AppTcpdf::GAP_Y);

        // payment method header
        $pdf->usePrimaryFont(13);
        $pdf->Cell(AppTcpdf::AVAILABLE_WIDTH, 0, $this->ts->trans('Payment Method'), $this->isDebug, 1);
        $pdf->drawInvoiceLineSeparator();
        $pdf->useNormalFont();
        $pdf->Ln(AppTcpdf::GAP_Y / 3);
        $pdf->Cell(AppTcpdf::AVAILABLE_WIDTH, 0, $this->ts->trans('Payment Method PDF help text'), $this->isDebug, 1);
        $pdf->Cell(AppTcpdf::AVAILABLE_WIDTH, 0, $this->pb->get('enterprise_ban'), $this->isDebug, 1);
        if ($invoice->getPaymentComments()) {
            $pdf->useBoldFont();
            $pdf->Ln(AppTcpdf::GAP_Y / 2);
            $pdf->Cell(AppTcpdf::AVAILABLE_WIDTH, 0, $invoice->getPaymentComments(), $this->isDebug, 1);
            $pdf->useNormalFont();
        }

        return $pdf;
    }
}

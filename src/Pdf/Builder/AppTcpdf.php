<?php

namespace App\Pdf\Builder;

use App\Manager\AssetsManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppTcpdf extends \TCPDF
{
    public const PDF_MARGIN_TOP = 48;
    public const PDF_MARGIN_BOTTOM = 20;
    public const PDF_MARGIN_LEFT = 20;
    public const PDF_MARGIN_RIGHT = 20;
    public const PDF_WIDTH = 210;
    public const PDF_HEIGHT = 297;
    public const AVAILABLE_WIDTH = self::PDF_WIDTH - self::PDF_MARGIN_LEFT - self::PDF_MARGIN_RIGHT;
    public const HALF_AVAILABLE_WIDTH = self::AVAILABLE_WIDTH / 2;
    public const GAP_Y = 10;
    private const PRIMARY_COLOR = [
        240,
        0,
        0,
    ];

    private ParameterBagInterface $pb;
    private AssetsManager $am;

    public function __construct(ParameterBagInterface $pb, AssetsManager $am)
    {
        parent::__construct();
        $this->pb = $pb;
        $this->am = $am;
    }

    public function Header(): void
    {
        // logo
        $this->SetXY(self::PDF_MARGIN_LEFT, 11);
        $this->Image($this->am->getLocalPublicPath('build/images/full_logo.png'), null, null, 25);
    }

    public function Footer(): void
    {
        $this->useNormalFont(7);
        $this->SetXY(self::PDF_MARGIN_LEFT, self::PDF_HEIGHT - self::PDF_MARGIN_BOTTOM + self::GAP_Y);
        $this->Cell(self::AVAILABLE_WIDTH, 0, $this->pb->get('enterprise_pdf_footer'), $this->pb->get('kernel.debug'), 0, 'C');
    }

    public function setFontStyle(?string $font = 'dejavusans', string $style = '', int $size = 12): void
    {
        $this->SetFont($font, $style, $size, '', true);
    }

    public function drawInvoiceLineSeparator(?float $y = null): void
    {
        $this->setLineStyle([
            'width' => 0.75,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => self::PRIMARY_COLOR,
        ]);
        $this->Line(
            self::PDF_MARGIN_LEFT,
            $y ?: $this->getY(),
            self::PDF_WIDTH - self::PDF_MARGIN_RIGHT,
            $y ?: $this->getY()
        );
        $this->setLineStyle([
            'width' => 0.25,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => [0, 0, 0],
        ]);
    }

    public function usePrimaryFont(int $size = 9): void
    {
        $this->SetTextColor(self::PRIMARY_COLOR[0], self::PRIMARY_COLOR[1], self::PRIMARY_COLOR[2]);
        $this->setFontStyle('dejavusans', 'B', $size);
    }

    public function useNormalFont(int $size = 9): void
    {
        $this->SetTextColor(0, 0, 0);
        $this->setFontStyle('dejavusans', '', $size);
    }

    public function useBoldFont(int $size = 9): void
    {
        $this->SetTextColor(0, 0, 0);
        $this->setFontStyle('dejavusans', 'B', $size);
    }
}

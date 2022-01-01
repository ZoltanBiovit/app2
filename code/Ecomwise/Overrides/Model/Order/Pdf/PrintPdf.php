<?php

namespace Ecomwise\Overrides\Model\Order\Pdf;

class PrintPdf extends \Bss\QuoteExtension\Model\Pdf\PrintPdf
{


    public function getPdf($quotes = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('quoteextension');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($quotes as $quote) {
            $page = $this->newPage();
            $requestQuote = $this->manageQuote->load($quote->getId(), 'quote_id');
            if (!$requestQuote->getId()) {
                $requestQuote = $this->manageQuote->load($quote->getId(), 'backend_quote_id');
            }

            /* Add image */
            $this->insertLogo($page, $quote->getStore());
            /* Add address */
            $this->insertAddress($page, $quote->getStore());

$this->drawTopHeader($page);

            /* Add head */
            $this->insertQuote(
                $page,
                $quote,
                $requestQuote
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Quote # ') . $requestQuote->getIncrementId());

            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            $canShowTotal = true;
            foreach ($quote->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getProductType() == 'configurable') {
                    $parentProductId = $item->getProductId();
                    $childProductSku = $item->getSku();
                    $canShowPrice = $this->cartHidePrice->canShowPrice($parentProductId, $childProductSku);
                } else {
                    $canShowPrice = $this->cartHidePrice->canShowPrice($item->getProductId(), false);
                }
                if (!$canShowPrice) {
                    $canShowTotal = false;
                }
                $item->setCanShowPrice($canShowPrice);
                /* Draw item */
                $this->drawQuoteItem($item, $page, $quote);
                $page = end($pdf->pages);
            }
            /* Add totals */
            if ($canShowTotal) {
                $this->insertTotals($page, $quote);
            }

$this->drawFooterCenter($page);
$this->drawFooterLeft($page);
$this->drawFooterRight($page);

        }
        $this->_afterGetPdf();
        return $pdf;
    }

    protected function drawTopHeader(\Zend_Pdf_Page $page) {
        $iFontSize = 10;     // font size
        $iWidthBorder = 545; // half page width

        $sNotice = "VARAŽDIN - CROATIA - OIB 73275412890"; // your message
        $sNotice2 = "tel. +385-42-260-001"; // your message
        $sNotice3 = "E-mail: info@biovit.hr";
        $sNotice4 = "fax.+385-42-260-021"; // your message
        $sNotice5 = "http://www.biovit.hr";
        $iXCoordinateText = 30;
        $sEncoding = 'UTF-8';
      //  $this->y -= 10; // move down on page
        try {
            $oFont = $this->_setFontRegular($page, $iFontSize);
            //$iXCoordinateText = $this->getAlignCenter($sNotice, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize);  // center text coordinate
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(0,50,0));                                             // red lines
            $iXCoordinateBorder = $iXCoordinateText - 5;                                                               // border is wider than text
            // draw top border
            $page->drawLine($iXCoordinateBorder, $this->y, $iXCoordinateBorder + $iWidthBorder, $this->y);
            // draw text
            $this->y -= 15;                                                                                             // further down
            $page->drawText($sNotice, $iXCoordinateText -5, $this->y, $sEncoding);
            $page->drawText($sNotice2, $iXCoordinateText + 315, $this->y, $sEncoding);
            $page->drawText($sNotice3, $iXCoordinateText + 445, $this->y, $sEncoding);
            $page->drawText($sNotice4, $iXCoordinateText + 315, $this->y - 10, $sEncoding);
            $page->drawText($sNotice5, $iXCoordinateText + 445, $this->y - 10, $sEncoding);
            $this->y -= 10; // further down
  
            $this->y -= 10;
        } catch (\Exception $exception) {
            // handle
        }
    }

    protected function drawFooterCenter(\Zend_Pdf_Page $page) {
        $iFontSize = 7;     // font size
        $iWidthBorder = 545; // half page width
        $iColumnWidth = 107;

        $sNotice = "Trgovački sud u Varaždinu, MBS 070010231. Temeljni kapital: 20.000,00 Kn uplaćen u cijelosti. Član uprave: Zoltan Vaštag"; // your message
        $sNotice2 = "Dokument je odobren elektronski u skladu s procedurama koje vrijede u Biovit d.o.o. i ne zahtjeva potpis."; // your message
        $sNotice3 = "Upisan u sudski registar kod Trgovačkog suda u";
        $sNotice4 = "Varaždinu, MBS 070010231.";
        $sNotice5 = "Temeljni kapital: 20.000,00 Kn uplaćen u cijelosti.";
        $sNotice6 = "Član uprave: Zoltan Vaštag";
        $sNotice7 = "OIB: 73275412890";
        $iXCoordinateText = 245;
        $sEncoding = 'UTF-8';

        try {
            $oFont = $this->_setFontRegular($page, $iFontSize);
            $iXCoordinateText = $this->getAlignCenter($sNotice, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize);  // center text coordinate
            $iXCoordinateText = $this->getAlignCenter($sNotice2, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize); 
        
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(0,50,0));
            $iXCoordinateBorder = $iXCoordinateText - 5;
            // draw top border
            $page->drawLine($iXCoordinateBorder, 90, $iXCoordinateBorder + $iWidthBorder, 90);
            // draw text
            $this->y -= 15;  // further down
            $page->drawText($sNotice, $iXCoordinateText + 95, 80, $sEncoding);
            $page->drawText($sNotice2, $iXCoordinateText + 120, 70, $sEncoding);
            $page->drawText($sNotice3, $iXCoordinateText + 190, 60, $sEncoding);
            $page->drawText($sNotice4, $iXCoordinateText + 190, 50, $sEncoding);
            $page->drawText($sNotice5, $iXCoordinateText + 190, 40, $sEncoding);
            $page->drawText($sNotice6, $iXCoordinateText + 190, 30, $sEncoding);
            $page->drawText($sNotice7, $iXCoordinateText + 190, 20, $sEncoding);
            $this->y -= 10; // further down
  
            $this->y -= 10;
        } catch (\Exception $exception) {
            // handle
        }
    }

    protected function drawFooterLeft(\Zend_Pdf_Page $page) {
        $iFontSize = 7;
        $iColumnWidth = 107;

        $sNotice = "BIOVIT d.o.o.";
        $sNotice2 = "Varaždinska ulica - odvojak II/15";
        $sNotice3 = "HR-42000 Jalkovec/Varaždin, Hrvatska";
        $sNotice4 = "Tel: +385 42 260 001          info@biovit.hr";
        $sNotice5 = "Fax: +385 42 260 021          www.biovit.hr";

        $iXCoordinateText = 245;
        $sEncoding = 'UTF-8';
 
        try {
            $oFont = $this->_setFontRegular($page, $iFontSize);
            $iXCoordinateText = $this->getAlignCenter($sNotice, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize);
            $iXCoordinateText = $this->getAlignCenter($sNotice2, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize); 
        
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(1, 0, 0));
         
          
            // draw text
            $this->y -= 15;  // further down
            $page->drawText($sNotice, $iXCoordinateText + -260, 60, $sEncoding);
            $page->drawText($sNotice2, $iXCoordinateText + -260, 50, $sEncoding);
            $page->drawText($sNotice3, $iXCoordinateText + -260, 40, $sEncoding);
            $page->drawText($sNotice4, $iXCoordinateText + -260, 30, $sEncoding);
            $page->drawText($sNotice5, $iXCoordinateText + -260, 20, $sEncoding);
            $this->y -= 10; // further down
  
            $this->y -= 10;
        } catch (\Exception $exception) {
            // handle
        }
    }

    protected function drawFooterRight(\Zend_Pdf_Page $page) {
        $iFontSize = 7;
        $iColumnWidth = 107;

        $sNotice = "Erste & SteierMaerkische bank d.d. Varšavska 3-5, Zagreb"; // your message
        $sNotice2 = "IBAN: HR5324020061100029320,    SWIFT: ESBC HR 22"; // your message
        $sNotice3 = "Raiffeisenbank Austria d.d. Petrinjska 59, Zagreb";
        $sNotice4 = "IBAN: HR6924840081104971600,    SWIFT: RZBH HR 2X";

        $iXCoordinateText = 245;
        $sEncoding = 'UTF-8';
 
        try {
            $oFont = $this->_setFontRegular($page, $iFontSize);
            $iXCoordinateText = $this->getAlignCenter($sNotice, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize);
            $iXCoordinateText = $this->getAlignCenter($sNotice2, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize); 
            $iXCoordinateText = $this->getAlignCenter($sNotice3, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize);
            $iXCoordinateText = $this->getAlignCenter($sNotice4, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize); 
        
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(1, 0, 0));
         
          
            // draw text
            $this->y -= 15;  // further down
            $page->drawText($sNotice, $iXCoordinateText + 260, 50, $sEncoding);
            $page->drawText($sNotice2, $iXCoordinateText + 260, 40, $sEncoding);
            $page->drawText($sNotice3, $iXCoordinateText + 260, 30, $sEncoding);
            $page->drawText($sNotice4, $iXCoordinateText + 260, 20, $sEncoding);
            $this->y -= 10; // further down
  
            $this->y -= 10;
        } catch (\Exception $exception) {
            // handle
        }
    }

}

<?php

require_once 'Tcpdf/tcpdf.php';

class My_InvoicePdfGenerator {

    protected $_baseCompany = "Yagga.cz";
    protected $_baseUserName = "Martina Jedináková";
    protected $_baseStreet = "Košetice 9";
    protected $_baseCity = "Velké Heraltice";
    protected $_baseZipCode = "747 75";
    protected $_baseName = "Martina Jedináková";
    protected $_baseIC = "03527671";
    protected $_basePath = "invoices";
    protected $_pdfPath;

    public function getPdfPath() {
        return $this->_pdfPath;
    }

    /*
     * GENERUJU PDF fakturu
     */

    public function generateInvoice($id, $creating_date, $due_date, $order, $orderItems) {

        // Include the main TCPDF library (search for installation path).
        //require_once('Tcpdf/tcpdf_include.php');
        // create new PDF document
        $pdf = new Tcpdf_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($this->_baseCompany);
        $pdf->SetTitle('Faktura č. ' . $id);
        $pdf->SetSubject('Faktura č. ' . $id);

// set default header data
        $pdf->SetHeaderData('', '', 'Faktura č. ' . $id);
        $pdf->setHeaderFont(Array('dejavusans', '', 13));

        $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// set header and footer fonts
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------
// set default font subsetting mode
        $pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 11, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
        $pdf->AddPage();


        $creating_date = date("j. n. Y", $creating_date);
        $due_date = date("j. n. Y", $due_date);

        // Set some content to print
        $html1 = <<<EOD
            <table>
            <tr>
                <td>
                    Objednávka: {$order->getOrder_number()}
                </td>
            </tr>
            <tr>
                <td>
                    Datum vystavení: {$creating_date}
                </td>
            </tr>
            <tr>
                <td>
                    Datum splatnosti: {$due_date}
                </td>
            </tr>
            <tr>
                <td>
                Vytvořil/a: {$this->_baseName}
                </td>
            </tr>


            </table>
EOD;

// Print text using writeHTMLCell()
//        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        $pdf->writeHTMLCell(180, 5, '', '', $html1, true, 1, false, 0, 'L');

        $pdf->Ln(5);

        /*
         * Dodavatel
         */
// Set some content to print
        $html2 = <<<EOD
        <table>
            <tr>
                <td>
                    <strong>Dodavatel:</strong>
                </td>
            </tr>
            <tr>
                <td>
                    {$this->_baseCompany}
                </td>
            </tr>        
            <tr>
                <td>
                    {$this->_baseUserName}
                </td>
            </tr>        
            <tr>
                <td>
                    {$this->_baseStreet}
                </td>
            </tr>        
            <tr>
                <td>
                    {$this->_baseZipCode} {$this->_baseCity}
                </td>
            </tr>            
            <tr>
                <td>
                    IČ: {$this->_baseIC}
                </td>
            </tr>
            <tr>
                <td>
                    Neplátce DPH.
                </td>
            </tr>
  
        </table>
EOD;

// Print text using writeHTMLCell()
        $pdf->writeHTMLCell(90, 5, '', '', $html2, true, 0, false, 0, 'L');

        /*
         * Odběratel
         */
// Set some content to print
        $html3 = <<<EOD
        <table>
            <tr>
                <td>
                    <strong>Odběratel:</strong>
                </td>
            </tr>
            <tr>
                <td>
                    {$order->getOrder_i_company()}
                </td>
            </tr>        
            <tr>
                <td>
                    {$order->getOrder_i_name()} {$order->getOrder_i_surname()}
                </td>
            </tr>        
            <tr>
                <td>
                    {$order->getOrder_i_street()}
                </td>
            </tr>        
            <tr>
                <td>
                    {$order->getOrder_i_zip_code()}  {$order->getOrder_i_city()}
                </td>
            </tr>            
            <tr>
                <td>
                    IČ: {$order->getOrder_i_ico()}
                </td>
            </tr>
            <tr>
                <td>
                    DIČ: {$order->getOrder_i_dic()}
                </td>
            </tr>    
        </table>
EOD;

// Print text using writeHTMLCell()
        $pdf->writeHTMLCell(90, 5, '', '', $html3, true, 1, false, 0, 'L');



        /*
         * zapsan v rejstriku
         */
// Set some content to print
        $html3_1 = <<<EOD
        <table>
            <tr>
                <td>
                    Podnikatel je zapsán do živnostenského rejstříku.
                </td>
            </tr>  
        </table>
EOD;

// Print text using writeHTMLCell()
        $pdf->writeHTMLCell(180, 5, '', '', $html3_1, true, 1, false, 0, 'L');




//print_r($orderItems);die;
        $orderItemsPdf = "";
        //vypis polozek objednavky
        foreach ($orderItems as $orderItem) {
            
            $variant = "";
            if($orderItem->getOrder_item_variant_name()){
                $variant = "<br />Varianta: ".$orderItem->getOrder_item_variant_name();
            }
            
            $orderItemsPdf .= "<tr>
                <td>" . $orderItem->getOrder_item_product_name() ."". $variant ."</td>
                <td>" . $orderItem->getOrder_item_pieces() . "</td>
                <td>" . $orderItem->getOrder_item_price() . " Kč</td>
                <td>" . $orderItem->getOrder_item_pieces() * $orderItem->getOrder_item_price() . " Kč</td>    
                    </tr>";
        }

        if (!empty($orderItemsPdf)) {
            $orderItemsPdf .= "<tr>
                <td colspan=\"3\" style=\"border-top: 0.5px solid #000;\">Doprava: " . $order->getOrder_delivery_name() . "</td>
                <td style=\"border-top: 0.5px solid #000;\">" . $order->getOrder_delivery_price() . " Kč</td>
                    </tr>";

            $orderItemsPdf .= "<tr>
                <td colspan=\"3\">Platba: " . $order->getOrder_payment_name() . "</td>
                <td>" . $order->getOrder_payment_price() . " Kč</td>
                    </tr>";

            $orderItemsPdf .= "<tr style=\"font-size: 14px;\">
                <td colspan=\"3\" style=\"text-align: right; border-top: 0.5px solid #000;\">
                    <strong>Celková hodnota faktury:</strong>
                </td>
                <td style=\"border-top: 0.5px solid #000;\">
                    <strong>{$order->getOrder_sum_with_delivery_payment()} Kč</strong>
                </td>
            </tr>";
        }


        // Set some content to print
        $html4 = <<<EOD
        <table>
        <tr style="width: 55%; background-color: #eee;">
            <td style="width: 55%; border-bottom: 1px solid #000;">
                <strong>Položka</strong>
            </td>
            <td style="width: 15%; border-bottom: 1px solid #000;">
                <strong>Množ.</strong>
            </td>
            <td style="width: 15%; border-bottom: 1px solid #000;">
                <strong>Cena/Kus</strong>
            </td>
            <td style="width: 15%; border-bottom: 1px solid #000;">
                <strong>Celkem</strong>
            </td>
        </tr>
                {$orderItemsPdf}
        </table>
EOD;

        $pdf->Ln(5);
        $pdf->writeHTMLCell(180, 5, '', '', $html4, true, 1, false, 0);


// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.

        $filterUrl = new Filter_Url();

        $invoicePath = 'faktura-' . $filterUrl->filter($id) . '.pdf';

        $this->_pdfPath = $invoicePath;

        $pdf->Output($this->_basePath . '/' . $invoicePath, 'F');
    }

}
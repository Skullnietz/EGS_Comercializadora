<?php
if (!class_exists('ExcelExportHelper')) {
    class ExcelExportHelper
    {
        public static function downloadXlsx($filenameBase, array $headers, array $rows, array $options = array())
        {
            $sheetName = isset($options['sheetName']) ? (string) $options['sheetName'] : 'Reporte';
            $sheetName = self::sanitizeSheetName($sheetName);

            $dateColumns = isset($options['dateColumns']) ? (array) $options['dateColumns'] : array();
            $currencyColumns = isset($options['currencyColumns']) ? (array) $options['currencyColumns'] : array();
            $hyperlinkColumns = isset($options['hyperlinkColumns']) ? (array) $options['hyperlinkColumns'] : array();

            $letters = self::columnLetters(count($headers));
            $maxLen = array();
            foreach ($headers as $i => $h) {
                $maxLen[$i] = mb_strlen((string) $h, 'UTF-8');
            }

            $sharedHyperlinks = array();
            $xmlRows = array();

            // Header row
            $headerCells = array();
            foreach ($headers as $c => $headerText) {
                $headerCells[] = self::inlineCell($letters[$c] . '1', (string) $headerText, 2);
            }
            $xmlRows[] = '<row r="1">' . implode('', $headerCells) . '</row>';

            // Data rows
            $rnum = 2;
            foreach ($rows as $row) {
                $cells = array();
                foreach ($headers as $c => $_) {
                    $value = isset($row[$c]) ? $row[$c] : '';
                    $display = is_null($value) ? '' : (string) $value;
                    $maxLen[$c] = max($maxLen[$c], mb_strlen($display, 'UTF-8'));

                    $ref = $letters[$c] . $rnum;

                    if (in_array($c, $dateColumns, true)) {
                        $serial = self::excelDateSerial($value);
                        if ($serial !== null) {
                            $cells[] = '<c r="' . $ref . '" s="1"><v>' . $serial . '</v></c>';
                            continue;
                        }
                    }

                    if (in_array($c, $currencyColumns, true)) {
                        $num = self::toNumber($value);
                        if ($num !== null) {
                            $cells[] = '<c r="' . $ref . '" s="3"><v>' . self::xmlNumber($num) . '</v></c>';
                            continue;
                        }
                    }

                    if (isset($hyperlinkColumns[$c])) {
                        $url = self::buildHyperlink($hyperlinkColumns[$c], $value, $row);
                        if ($url !== '') {
                            $relId = 'rId' . (count($sharedHyperlinks) + 1);
                            $sharedHyperlinks[] = array('ref' => $ref, 'url' => $url, 'rid' => $relId);
                            $cells[] = self::inlineCell($ref, $display, 4);
                            continue;
                        }
                    }

                    $num = self::toNumber($value);
                    if ($num !== null && self::looksNumeric($value)) {
                        $cells[] = '<c r="' . $ref . '"><v>' . self::xmlNumber($num) . '</v></c>';
                    } else {
                        $cells[] = self::inlineCell($ref, $display, 0);
                    }
                }
                $xmlRows[] = '<row r="' . $rnum . '">' . implode('', $cells) . '</row>';
                $rnum++;
            }

            $lastCol = end($letters);
            $lastRow = max(1, count($rows) + 1);
            $dimension = 'A1:' . $lastCol . $lastRow;

            $colsXml = array();
            foreach ($maxLen as $i => $len) {
                $width = min(80, max(10, $len + 2));
                $index = $i + 1;
                $colsXml[] = '<col min="' . $index . '" max="' . $index . '" width="' . $width . '" customWidth="1"/>';
            }

            $hyperlinksXml = '';
            if (!empty($sharedHyperlinks)) {
                $tmp = array();
                foreach ($sharedHyperlinks as $hl) {
                    $tmp[] = '<hyperlink ref="' . $hl['ref'] . '" r:id="' . $hl['rid'] . '"/>';
                }
                $hyperlinksXml = '<hyperlinks>' . implode('', $tmp) . '</hyperlinks>';
            }

            $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
                . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . '<dimension ref="' . $dimension . '"/>'
                . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
                . '<sheetFormatPr defaultRowHeight="15"/>'
                . '<cols>' . implode('', $colsXml) . '</cols>'
                . '<sheetData>' . implode('', $xmlRows) . '</sheetData>'
                . $hyperlinksXml
                . '</worksheet>';

            $sheetRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
            foreach ($sharedHyperlinks as $hl) {
                $sheetRelsXml .= '<Relationship Id="' . $hl['rid'] . '" '
                    . 'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" '
                    . 'Target="' . self::xmlEscapeAttr($hl['url']) . '" TargetMode="External"/>';
            }
            $sheetRelsXml .= '</Relationships>';

            $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                . '<numFmts count="2">'
                . '<numFmt numFmtId="164" formatCode="yyyy-mm-dd hh:mm"/>'
                . '<numFmt numFmtId="165" formatCode="$#,##0.00"/>'
                . '</numFmts>'
                . '<fonts count="3">'
                . '<font><sz val="11"/><name val="Calibri"/></font>'
                . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
                . '<font><u/><color rgb="FF0563C1"/><sz val="11"/><name val="Calibri"/></font>'
                . '</fonts>'
                . '<fills count="2">'
                . '<fill><patternFill patternType="none"/></fill>'
                . '<fill><patternFill patternType="solid"><fgColor rgb="FFEFF6FF"/><bgColor indexed="64"/></patternFill></fill>'
                . '</fills>'
                . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
                . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
                . '<cellXfs count="5">'
                . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
                . '<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
                . '<xf numFmtId="0" fontId="1" fillId="1" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
                . '<xf numFmtId="165" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
                . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
                . '</cellXfs>'
                . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
                . '</styleSheet>';

            $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
                . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . '<sheets><sheet name="' . self::xmlEscapeAttr($sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
                . '</workbook>';

            $rootRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
                . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
                . '</Relationships>';

            $workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                . '</Relationships>';

            $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                . '<Default Extension="xml" ContentType="application/xml"/>'
                . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
                . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
                . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
                . '</Types>';

            $now = gmdate('Y-m-d\TH:i:s\Z');
            $coreXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
                . 'xmlns:dc="http://purl.org/dc/elements/1.1/" '
                . 'xmlns:dcterms="http://purl.org/dc/terms/" '
                . 'xmlns:dcmitype="http://purl.org/dc/dcmitype/" '
                . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
                . '<dc:creator>EGS</dc:creator>'
                . '<cp:lastModifiedBy>EGS</cp:lastModifiedBy>'
                . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>'
                . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified>'
                . '</cp:coreProperties>';

            $appXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" '
                . 'xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
                . '<Application>EGS Exporter</Application>'
                . '</Properties>';

            $tmp = tempnam(sys_get_temp_dir(), 'egs_xlsx_');
            $xlsxPath = $tmp . '.xlsx';
            @unlink($xlsxPath);

            $zip = new ZipArchive();
            if ($zip->open($xlsxPath, ZipArchive::CREATE) !== true) {
                @unlink($tmp);
                throw new Exception('No se pudo generar el archivo XLSX.');
            }

            $zip->addFromString('[Content_Types].xml', $contentTypesXml);
            $zip->addFromString('_rels/.rels', $rootRelsXml);
            $zip->addFromString('docProps/core.xml', $coreXml);
            $zip->addFromString('docProps/app.xml', $appXml);
            $zip->addFromString('xl/workbook.xml', $workbookXml);
            $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRelsXml);
            $zip->addFromString('xl/styles.xml', $stylesXml);
            $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
            $zip->addFromString('xl/worksheets/_rels/sheet1.xml.rels', $sheetRelsXml);
            $zip->close();

            @unlink($tmp);

            $safeName = self::sanitizeFilename($filenameBase) . '.xlsx';
            header('Expires: 0');
            header('Cache-Control: private, must-revalidate');
            header('Pragma: public');
            header('Content-Description: File Transfer');
            header('Content-Transfer-Encoding: binary');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $safeName . '"');
            header('Content-Length: ' . filesize($xlsxPath));
            readfile($xlsxPath);
            @unlink($xlsxPath);
            exit;
        }

        private static function inlineCell($ref, $value, $style)
        {
            return '<c r="' . $ref . '" t="inlineStr" s="' . intval($style) . '"><is><t>' . self::xmlEscape((string) $value) . '</t></is></c>';
        }

        private static function sanitizeFilename($name)
        {
            $name = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $name);
            return trim($name, '_') ?: 'reporte';
        }

        private static function sanitizeSheetName($name)
        {
            $name = trim((string) $name);
            $name = str_replace(array('\\', '/', '*', '?', ':', '[', ']'), ' ', $name);
            if ($name === '') {
                $name = 'Reporte';
            }
            return mb_substr($name, 0, 31, 'UTF-8');
        }

        private static function columnLetters($count)
        {
            $out = array();
            for ($i = 0; $i < $count; $i++) {
                $n = $i + 1;
                $s = '';
                while ($n > 0) {
                    $m = ($n - 1) % 26;
                    $s = chr(65 + $m) . $s;
                    $n = (int)(($n - $m) / 26);
                }
                $out[] = $s;
            }
            return $out;
        }

        private static function xmlEscape($s)
        {
            return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        }

        private static function xmlEscapeAttr($s)
        {
            return htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
        }

        private static function toNumber($value)
        {
            if (is_int($value) || is_float($value)) {
                return (float) $value;
            }
            if (!is_string($value)) {
                return null;
            }
            $v = trim($value);
            if ($v === '') {
                return null;
            }
            $v = str_replace(array('$', ',', ' '), '', $v);
            if (!is_numeric($v)) {
                return null;
            }
            return (float) $v;
        }

        private static function looksNumeric($value)
        {
            if (is_int($value) || is_float($value)) {
                return true;
            }
            if (!is_string($value)) {
                return false;
            }
            $v = trim($value);
            if ($v === '') {
                return false;
            }
            if (preg_match('/^0\d+$/', $v)) {
                return false;
            }
            $v = str_replace(array('$', ',', ' '), '', $v);
            return is_numeric($v);
        }

        private static function xmlNumber($number)
        {
            return rtrim(rtrim(number_format((float) $number, 10, '.', ''), '0'), '.');
        }

        private static function excelDateSerial($value)
        {
            if ($value instanceof DateTime) {
                $ts = $value->getTimestamp();
            } else {
                $s = trim((string) $value);
                if ($s === '') {
                    return null;
                }
                $ts = strtotime($s);
            }
            if (!$ts) {
                return null;
            }
            return (($ts / 86400) + 25569);
        }

        private static function buildHyperlink($rule, $value, array $row)
        {
            if (is_callable($rule)) {
                $url = call_user_func($rule, $value, $row);
                return is_string($url) ? trim($url) : '';
            }

            if ($rule === 'whatsapp_api') {
                $digits = preg_replace('/\D+/', '', (string) $value);
                if ($digits === '') {
                    return '';
                }
                if (strlen($digits) === 10) {
                    $digits = '52' . $digits;
                }
                return 'https://api.whatsapp.com/send?phone=' . $digits;
            }

            return '';
        }
    }
}

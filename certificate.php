<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A4_non_embedded certificate type
 *
 * @package    mod_certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (isset($certrecord->timearchived)) {
    // Use archived values
    $timecompleted = certificate_get_date_completed_formatted($certificate, $certrecord->timecompleted);
    $grade = $certrecord->grade;
    $outcome = $certrecord->outcome;
    $code = $certrecord->code;
} else {
    $timecompleted = certificate_get_date($certificate, $certrecord, $course);
    $grade = certificate_get_grade($certificate, $course);
    $outcome = certificate_get_outcome($certificate, $course);
    $code = certificate_get_code($certificate, $certrecord);
}
$userdevisiondetail = $DB->get_record_sql("SELECT uid.id,uid.data from {user_info_data} as uid INNER JOIN {user_info_field} as uif ON uif.id = uid.fieldid WHERE userid = ? AND shortname =?",array($USER->id,'divisionname'));



$pdf = new PDF($certificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($certificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();


// Define variables
// Landscape
if ($certificate->orientation == 'L') {
    $x = 67;
    $y = 35;
    $sealx = 230;
    $sealy = 150;
    $sigx = 47;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 40;
    $wmarky = 31;
    $wmarkw = 212;
    $wmarkh = 148;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
    
    // Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text
$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $x, $y, 'L', 'Helvetica', 'B', 35, get_string('title', 'certificate'));
$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $x, $y + 23, 'L', 'Helvetica', '', 25, get_string('certify', 'certificate'));
certificate_print_text($pdf, $x, $y + 37, 'L', 'Helvetica', 'B', 35, fullname($USER));
if(!empty($userdevisiondetail->data)){
    if($userdevisiondetail->data == 'Poultry' || $userdevisiondetail->data == 'Veterinary'){
    certificate_print_logo($pdf, $certificate,'Vetpoultry', $x, '10' , '', '20');
}else{
    certificate_print_logo($pdf, $certificate, $userdevisiondetail->data, $x, '10' , '', '20');
}
certificate_print_text($pdf, $x, $y + 55, '', 'Helvetica', '', 25, get_string('divisionname', 'certificate'));
certificate_print_text($pdf, $x+43, $y + 55, '', 'Helvetica', '', 25, $userdevisiondetail->data);
certificate_print_text($pdf, $x, $y + 71, 'L', 'Helvetica', '', 22, get_string('statement', 'certificate'));
certificate_print_text($pdf, $x, $y + 83, 'L', 'Helvetica', 'B', 35, format_string($course->fullname));
certificate_print_text($pdf, $x, $y + 116, 'L', 'Helvetica', '', 22, $timecompleted);
}else{
    certificate_print_text($pdf, $x, $y + 55, 'L', 'Helvetica', '', 22, get_string('statement', 'certificate'));
certificate_print_text($pdf, $x, $y + 80, 'L', 'Helvetica', 'B', 35, format_string($course->fullname));
certificate_print_text($pdf, $x, $y + 116, 'L', 'Helvetica', '', 22, $timecompleted);
    }
certificate_print_text($pdf, $x, $y + 104, 'L', 'Helvetica', '', 22, $grade);
certificate_print_text($pdf, $x, $y + 124, 'L', 'Times', '', 10, $outcome);
if ($certificate->printhours) {
    certificate_print_text($pdf, $x, $y + 122, 'L', 'Times', '', 10, get_string('credithours', 'certificate') . ': ' . $certificate->printhours);
}
certificate_print_text($pdf, $x, $codey, 'L', 'Times', '', 10, $code);
$i = 0;
if ($certificate->printteacher) {
    $context = context_module::instance($cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/certificate:printteacher', '', $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            certificate_print_text($pdf, $sigx, $sigy + ($i * 4), 'L', 'Times', '', 12, fullname($teacher));
        }
    }
}

certificate_print_text($pdf, $custx, $custy, 'L', null, null, null, $certificate->customtext);
    
} else { // Portrait
    $x = 46;
    $y = 60;
    $sealx = 150;
    $sealy = 220;
    $sigx = 30;
    $sigy = 230;
    $custx = 30;
    $custy = 230;
    $wmarkx = 26;
    $wmarky = 58;
    $wmarkw = 158;
    $wmarkh = 170;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 210;
    $brdrh = 297;
    $codey = 250;
    
    // Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text
$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $x, $y, 'L', 'Helvetica', 'B', 25, get_string('title', 'certificate'));
$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $x, $y + 25, 'L', 'Helvetica', '', 18, get_string('certify', 'certificate'));
certificate_print_text($pdf, $x, $y + 40, 'L', 'Helvetica', 'B', 25, fullname($USER));
if(!empty($userdevisiondetail->data)){
certificate_print_text($pdf, $x, $y + 57, '', 'Helvetica', '', 18, get_string('divisionname', 'certificate'));
certificate_print_text($pdf, $x+30, $y + 57, '', 'Helvetica', '', 18, $userdevisiondetail->data);
certificate_print_text($pdf, $x, $y + 70, 'L', 'Helvetica', '', 18, get_string('statement', 'certificate'));
certificate_print_text($pdf, $x, $y + 115, 'L', 'Helvetica', 'B', 25, format_string($course->fullname));
certificate_print_text($pdf, $x, $y + 165, 'L', 'Helvetica', '', 20, $timecompleted);   
}else{
    certificate_print_text($pdf, $x, $y + 57, 'L', 'Helvetica', '', 18, get_string('statement', 'certificate'));
certificate_print_text($pdf, $x, $y + 105, 'L', 'Helvetica', 'B', 25, format_string($course->fullname));
certificate_print_text($pdf, $x, $y + 165, 'L', 'Helvetica', '', 20, $timecompleted);
    }

certificate_print_text($pdf, $x, $y + 102, 'L', 'Times', '', 10, $grade);
certificate_print_text($pdf, $x, $y + 112, 'L', 'Times', '', 10, $outcome);
if ($certificate->printhours) {
    certificate_print_text($pdf, $x, $y + 122, 'L', 'Times', '', 10, get_string('credithours', 'certificate') . ': ' . $certificate->printhours);
}
certificate_print_text($pdf, $x, $codey, 'L', 'Times', '', 10, $code);
$i = 0;
if ($certificate->printteacher) {
    $context = context_module::instance($cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/certificate:printteacher', '', $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            certificate_print_text($pdf, $sigx, $sigy + ($i * 4), 'L', 'Times', '', 12, fullname($teacher));
        }
    }
}

certificate_print_text($pdf, $custx, $custy, 'L', null, null, null, $certificate->customtext);
}

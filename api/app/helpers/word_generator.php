<?php
//
//function createWordFile($data, $imgSource,$toInfo,$attach)
//{
//    require APPROOT . '/vendor/autoload.php';
//
//    //This is the main document in  Template.docx file.
//    $file = URLROOT . "/public/template.docx";
//    $phpword = new \PhpOffice\PhpWord\TemplateProcessor($file);
//
//
//    $phpword->setValue('${toPerson}',  $data->toTxt);
//    $phpword->setValue('${variableName}', $data->subject);
//    $phpword->setValue('${depName}', $data->fromDep);
//    $phpword->setValue('${depNameEn}', $data->fromDepEn);
//    $phpword->setValue('${content}', $data->content);
//    $phpword->setValue('${signatory}', $data->signatory);
//    $phpword->setValue('${position}', $data->position);
//    $phpword->setValue('${signDate}', $data->signDate);
//    $phpword->setValue('${creator}', $data->name);
//    $phpword->setValue('${thanks}', $data->thanks);
//    $phpword->setValue('${greeting}', $data->greeting);
//    $phpword->setValue('${toDo}', $toInfo);
//    $phpword->setValue('${attach}', $attach);
//    $phpword->setImageValue('image2.png', $imgSource);
//
////    unlink($filePath);
//    $name = str_replace(' ', '_',  $data->fileName);
//    $filename = $name.'_'. $data->number . '.docx';
//    $phpword->saveAs($filename);
//    return $filename;
//}
//

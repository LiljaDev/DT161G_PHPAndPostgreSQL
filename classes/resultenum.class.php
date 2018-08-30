<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: resultenum.class.php
 * Desc: Constants representing query results for Projekt
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

abstract class ResultEnum{
    const QUERY_FAILED = 0;
    const OK = 1;
    const IMAGE_DUPLICATE = 2;
    const USER_NOT_FOUND = 3;
    const CATEGORY_NOT_FOUND_OR_EMPTY = 4;
}
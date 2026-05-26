<?php

function get_competencies_for_user_and_status($userid){
    global $DB;
    $sql="
        SELECT
            c.id,
            c.shortname,
            /*c.description,*/
            CASE c.parentid
                WHEN 0 THEN 1
                ELSE 0
            END AS is_title_area,
            CASE cu.status
                WHEN 0 THEN 'in progress'
                ELSE '-'
            END AS estado,
            CASE cu.proficiency
                WHEN 1 THEN 'approved'
                ELSE '-'
            END AS competency_proficiency,
            CASE SUBSTRING(c.shortname, 4, 1)
                WHEN 'A' THEN '#0066FF'
                WHEN 'D' THEN '#FF6600'
                WHEN 'L' THEN '#6600FF'
                ELSE 'title'
            END AS STR_lvl,
            CASE SUBSTRING(c.shortname, 1, 1)
                WHEN '6' THEN '#C000FF'
                WHEN '7' THEN '#C000FF'
                WHEN '8' THEN '#C000FF'
                ELSE 'other'
            END AS more_lvl
        FROM {competency} c
        LEFT JOIN {competency_usercomp} cu
            ON cu.competencyid = c.id and cu.userid=$userid
        WHERE c.path LIKE '%/0/%' and c.competencyframeworkid =".get_idnumber_frameword()." ORDER BY c.shortname ASC;
            ";
    try {
        $competency_status=[];
        $competency_status=$DB->get_records_sql($sql);
        // echo"<pre>";var_dump($competency_status);echo"</pre>";
        return $competency_status;
    }catch (dml_exception $e) {
        error_log( $e->getMessage()); 
        return false; 
    }
}

function get_course_for_competenci($competencyid){
    global $DB;
    $sql="
    SELECT
        cc.courseid,
        cc.competencyid,
        c.fullname
    FROM
        {competency_coursecomp} cc
    JOIN {course} c ON
        c.id = cc.courseid
    WHERE
        competencyid = $competencyid AND c.visible = 1;
    ";
    try {
        $course=[];
        $course=$DB->get_records_sql($sql);
        return $course;
    }catch (dml_exception $e) {
        error_log( $e->getMessage()); 
        return false; 
    }
}
function list_learning_plans($userid, $num_circle){
    global $DB;
    $sql="
            SELECT
                t.id AS templateid,
                t.shortname AS templatename,
                lp.id AS learningplanid,
                lp.name AS learningplanname,
                CASE WHEN lp.userid = $userid THEN TRUE ELSE FALSE
            END AS matriculado,
            COUNT(c.id) AS num_competencies,
            COUNT(
                CASE WHEN uc.proficiency = 1 THEN 1 ELSE NULL
            END
            ) AS competency_completed,
            SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.shortname, '(',-1),')',1),'-',1 )
            AS LvL,
            SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.shortname, '(',-1),')',1),'-',-1 )AS lang_lp,
            0 can_enroll
            FROM
                {competency_template} t
            LEFT JOIN {competency_plan} lp ON
                t.id = lp.templateid AND lp.userid = $userid
            JOIN {competency} c ON
                c.idnumber LIKE '$num_circle%'
            LEFT JOIN {competency_usercomp} uc ON
                uc.competencyid = c.id AND uc.userid = $userid
            JOIN {competency_templatecomp} ctt ON
                t.id = ctt.templateid AND ctt.competencyid = c.id
            GROUP BY
                t.id,
                lp.id ORDER BY `templatename` ASC;
    ";

    try {
        $learning_plans=[];
        $learning_plans=$DB->get_records_sql($sql);
        return $learning_plans;

    }catch (dml_exception $e) {
        error_log( $e->getMessage()); 
        return false; 
    }
}

function list_courses_avalible($id_user) {
    $paths_categori = get_competenci_and_id_category_main();

    if (empty($paths_categori)) {
        echo "No se encontraron categorías o competencias disponibles.";
        return;
    }
    $competenci_proficiency = [];
    $all_competenci = [];

 // Obtener competencias aprobadas por el usuario por categoría
    try {
        foreach ($paths_categori as $path) {
            $competenci_proficiency[$path->id] = get_competency_aprobadas($path->id, $id_user);
        }        

        // Obtener todas las competencias disponibles por categoría
        foreach ($paths_categori as $path) {
            $all_competenci[$path->id] = get_all_competencies_area($path->id);
        }
       //var_dump($all_competenci);

       foreach ($paths_categori as $path) {
        // Obtener todas las competencias del área
        $all_competencies = get_all_competencies_area($path->id);
        
        foreach ($all_competencies as $competency) {
            // Verificar si la competencia está aprobada
            $is_approved = isset($competenci_proficiency[$path->id][$competency->id]);
    
            $competencies_with_status[$path->id][$competency->id] = [
                'id' => $competency->id,
                'shortname' => $competency->shortname,
                'approved' => $is_approved ? get_string('Completed', 'block_ideal_cstatus') : get_string('Pending', 'block_ideal_cstatus'),
            ];
        }
    }
        return $competencies_with_status;
    //var_dump($competencies_with_status);die();

    }catch (dml_exception $e) {
        error_log( $e->getMessage()); 
        return false; 
    }  
}
//new 19/12
function get_competenci_and_id_category_main(){
    global $DB;
    $sql="
        SELECT
            c.id
        FROM
            {competency} c
        WHERE
        c.path LIKE '/0/' AND c.competencyframeworkid =".get_idnumber_frameword()."
    ";
    try {
        $competency_id=[];
        $competency_id=$DB->get_records_sql($sql);
        return $competency_id;
    } catch (dml_exception $e) {
        error_log( $e->getMessage()); 
        return false; 
    }
}
//Usuarios con competencias aprobadas
function get_competency_aprobadas($path, $id_user) {
    global $DB;
    $sql = "
        SELECT
            cu.competencyid as id
        FROM {user} u
        JOIN {competency_usercomp} cu ON cu.userid = u.id
        JOIN {competency} c ON cu.competencyid = c.id
        WHERE c.path LIKE '/0/$path/%' AND u.id = :userid
    ";
    try{
        $params = ['userid' => $id_user];
        $competences_user_proficiency = $DB->get_records_sql($sql, $params);
        return $competences_user_proficiency;
    }catch (dml_exception $e) {
        error_log( $e->getMessage()); 
        return false; 
    }
}
function get_all_competencies_area($path){
    global $DB;
    // Validamos que el parámetro $path no esté vacío
    if (empty($path)) {
        return false; // Retornamos false si el parámetro no es válido.
    }
    $sql = "
        SELECT DISTINCT
            c.id,
            c.shortname
        FROM
            {competency} c
        WHERE
            path LIKE '/0/$path/%' AND path NOT LIKE '/0/';
    ";
    try {
        $competences_for_area = $DB->get_records_sql($sql);
        if ($competences_for_area === false) {
            return false;
        }
        return $competences_for_area; // Retornamos los registros obtenidos.
    } catch (dml_exception $e) {
        error_log( $e->getMessage()); 
        return false; 
    }
}
// add 19/12
function get_role_user($id_user) {
    global $DB;
    try {
        $sql = "
            SELECT DISTINCT
                r.shortname
            FROM
                {role} r
            JOIN {role_assignments} ra ON
                ra.roleid = r.id
            WHERE
                ra.userid = :userid
        ";
        return $DB->get_records_sql($sql, ['userid' => $id_user]);
    } catch (Exception $e) {
        error_log("Error al obtener el rol del usuario: " . $e->getMessage());
        return null;
    }
}

function get_legend(){

    $legend = [
        ['name' => 'str_A', 'color' => '#0066FF'],
        ['name' => 'str_D', 'color' => '#FF6600'],
        ['name' => 'str_L', 'color' => '#6600FF'],
        ['name' => 'str_Soft', 'color' => '#C000FF'],
        ['name' => 'str_Ethi', 'color' => '#C000FF'],
        ['name' => 'str_IA', 'color' => '#C000FF'],
    ];

    for ($i = 0; $i < count($legend); $i++) {
        $legend[$i]['name'] = get_string($legend[$i]['name'], 'block_ideal_cstatus');
        $legend[$i]['color'] = get_string($legend[$i]['color'], 'block_ideal_cstatus');
    }
    return json_encode($legend);
}


function get_list_users(){
    global $DB;

    try {
        $sql = "
            SELECT DISTINCT
                u.id,
                u.firstname,
                u.lastname,
                u.firstnamephonetic,
                u.lastnamephonetic,
                u.middlename,
                u.alternatename,
                u.email,
                u.country
            FROM
                {user} u
            JOIN {competency_usercomp} cu ON
                cu.userid = u.id
            WHERE
                cu.proficiency = 1
            ORDER BY
                u.firstname ASC
        ";

        // Obtiene los usuarios
        $users = $DB->get_records_sql($sql);

        // Procesamos los usuarios para crear un array de datos adecuados
        $users_ok = [];
        foreach ($users as $user) {
            $users_ok[] = [
                'id' => $user->id,
                'fullname' => fullname($user), // Usamos la función fullname para generar el nombre completo
                'email' => $user->email,
                'country' => $user->country
            ];
        }
        return $users_ok;

    } catch (Exception $e) {
        error_log("Error al obtener la lista de usuarios: " . $e->getMessage());
        return [];
    }
}

function get_list_countries(){
    global $DB;

    try {
        $sql = "
            SELECT 
                u.country,
                count(u.id) as n_users
            FROM
                {user} as u
            WHERE
                u.country <> ''
            GROUP BY
                u.country
            HAVING
                count(u.id)>1
        ";
        $countries = $DB->get_records_sql($sql);
        $countries_ok = [];

        foreach ($countries as $country) {
            $countries_ok[] = strtoupper($country->country)." (".get_string(strtoupper($country->country), 'countries').")";
        }
        // var_dump($countries);
        $countries_ok[] = get_string('all_users', 'block_ideal_cstatus');
        $countries_ok[] = get_string('users_not_country', 'block_ideal_cstatus');
        return $countries_ok;
    } catch (Exception $e) {
        error_log("Error al obtener la lista de paises: " . $e->getMessage());
        return [];
    }
}


function get_idnumber_frameword() {
    global $DB;
    try {
        $sql_get_id_frameword = 'SELECT id FROM {competency_framework} WHERE idnumber = 2024';
        $id_frameword = $DB->get_field_sql($sql_get_id_frameword);
        return (int)$id_frameword;
    } catch (Exception $e) {
        error_log("Error al obtener el ID del framework: " . $e->getMessage());
        return 0; // Retorna 0 en caso de error
    }
}

function get_idnumber_competence_nivel($name_competence){
    global $DB;
    try {
        $sql_get_idnumber_competence = "
            SELECT idnumber FROM {competency} WHERE shortname = :shortname
        ";
        return $DB->get_field_sql($sql_get_idnumber_competence, ['shortname' => $name_competence]);
    } catch (Exception $e) {
        error_log("Error al obtener el ID de competencia: " . $e->getMessage());
        return null; // Retorna null en caso de error
    }
}
function select_area_name($num){
    try {
        $sql = "
            SELECT DISTINCT
                c.id,
                c.shortname AS competenciaa
            FROM
                {competency} c
            JOIN {competency_usercomp} cu JOIN {competency_coursecomp} cc 
            WHERE
                c.shortname LIKE '$num. %'
        ";
        return $sql;
    } catch (Exception $e) {
        error_log("Error en la consulta select_area_name: " . $e->getMessage());
        return null;
    }
}
//0 busqueda normal, 1 Admin search user
function select_user_competence_complete($id_user,$search_admin){
    try {
        $id_frameword = get_idnumber_frameword();
        $sql = "
            SELECT DISTINCT
                c.shortname AS competencia_ok
            FROM
                {competency_usercomp} cu
            JOIN {user} u ON
                u.id = cu.userid
            JOIN {competency} c ON
                c.id = cu.competencyid
            JOIN {competency_framework} cf ON
                cf.id = $id_frameword
            WHERE
                cu.proficiency = 1 AND u.id = $id_user
                order by `competencia_ok` ASC;
        ";
        return $sql;
    } catch (Exception $e) {
        error_log("Error al seleccionar competencias completas del usuario: " . $e->getMessage());
        return null;
    }
}

function get_cabeceras(){
    try {
        $id_frameword = get_idnumber_frameword();
        $sql = "
            SELECT DISTINCT
                c.id,
                c.shortname AS cabecera
            FROM
                {competency} c
            WHERE
                c.path = '/0/' AND c.competencyframeworkid = $id_frameword
            ORDER BY
                `cabecera` ASC
        ";
        return $sql;
    } catch (Exception $e) {
        error_log("Error al obtener las cabeceras: " . $e->getMessage());
        return null;
    }
}

function get_ids_cabeceras_db($sql){
    global $DB;
    try {
        $datos = $DB->get_records_sql($sql, null);
        $ids = [];

        foreach ($datos as $dato) {
            $ids[] = $dato->id;
        }
       // var_dump($ids);
        // Invertimos los últimos dos campos por las áreas 7 y 6
        $count = count($ids);

        return $ids;
    } catch (Exception $e) {
        error_log("Error al obtener IDs de cabeceras: " . $e->getMessage());
        return [];
    }
}

function consultas_por_areas($id_parentid){
    $consultas = [];
    try {
        $id_frameword = get_idnumber_frameword();
        for ($i = 0; $i < count($id_parentid); $i++) {
            $id = $id_parentid[$i];

            $consultas['consulta' . $i] = "
                SELECT DISTINCT
                    c.id,
                    c.shortname,
                    LEFT(c.shortname, 4) AS competenciaa,
                    c.idnumber as id_numbers_competencias_,
                    c.path,
                    c.parentid
                FROM
                    {competency} c
                WHERE
                    c.parentid = $id AND c.competencyframeworkid = $id_frameword
            ";
        }
        return $consultas;
    } catch (Exception $e) {
        error_log("Error al generar consultas por áreas: " . $e->getMessage());
        return [];
    }
}

function get_competencias_por_area() {
    try {
        $id_frameword = get_idnumber_frameword();
        $ids = get_ids_cabeceras_db(get_cabeceras());

        $sub_cabeceras_sql_ = [];

        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];

            $sub_cabeceras_sql_[$i] = "
                SELECT DISTINCT
                    c.id,
                    c.shortname
                FROM
                    {competency} c
                WHERE
                    c.parentid = $id AND c.competencyframeworkid = $id_frameword
            ";
        }
        return $sub_cabeceras_sql_;
    } catch (Exception $e) {
        error_log("Error al obtener competencias por área: " . $e->getMessage());
        return [];
    }
}

function get_ids(){
    global $DB;
    try {
        $consultas_sub_cabeceras = get_competencias_por_area();
        $ids_consultas = [];

        for ($i = 0; $i < count($consultas_sub_cabeceras) - 2; $i++) {
            $ids_consultas['consulta' . $i] = $DB->get_records_sql($consultas_sub_cabeceras[$i], null);
        }
        return $ids_consultas;
    } catch (Exception $e) {
        error_log("Error al obtener IDs: " . $e->getMessage());
        return [];
    }
}
//BORRAR?
function learning_for_competency($id_competency){
    global $DB;
    $sql="
        SELECT
            ct.id AS id_template,
            ct.shortname,
            cttc.templateid,
            cttc.competencyid AS id_compe_template
        FROM
            {competency_template} ct
        JOIN {competency_templatecomp} cttc ON
            ct.id = cttc.templateid
        WHERE
            cttc.competencyid = :id_competency
    ";
    try {
        $params = ['id_competency' => $id_competency];
        $result = $DB->get_records_sql($sql, $params);
        return $result;
    } catch (dml_exception $e) {
        error_log($e->getMessage());
        return false;
    }
}
function cohort_for_templates($templateid){
    global $DB;
    /*
    id
id templateid cohortid shortname name
7   5         7        1 Professional engagement (AD-en) 1.1 Organizational communication cohorte es
12  5         19       1 Professional engagement (AD-en) 1.1 Organizational communication cohorte lv
    */
    $sql="
        SELECT
        ctc.id,
        ctc.templateid,
        ctc.cohortid,
        ct.shortname,
        c.name
    FROM
        {competency_templatecohort} ctc
    JOIN {competency_template} ct ON
        ct.id = ctc.templateid
    join {cohort} c ON
        ctc.cohortid=c.id
    WHERE ctc.templateid= :templateid;
    ";
    try {
        $params = ['templateid' => $templateid];
        $result = $DB->get_records_sql($sql, $params);
        //var_dump($result);die();
        return $result;
    } catch (dml_exception $e) {
        error_log($e->getMessage());
        return false;
    }
}
function get_cohort_per_id_template($id_template_cohort, $lang_profile_user){
    global $DB;
    $sql="
        SELECT
            ctc.id AS id_template_cohort,
            ctc.templateid AS id_template_id,
            ctc.cohortid,
            c.name,
            c.idnumber
        FROM
            {competency_templatecohort} ctc
        JOIN {cohort} c ON
            c.id = ctc.cohortid AND ctc.templateid = :id_template_cohort
        WHERE
            c.idnumber LIKE :lang_profile_user
    ";
    try {
        $params = [
            'id_template_cohort' => $id_template_cohort,
            'lang_profile_user' => '%' . $lang_profile_user . '%'
        ];
        $result = $DB->get_records_sql($sql, $params);
        return $result;
    } catch (dml_exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

function user_in_cohort($id_user, $cohort_id) {
    global $DB;
    $result=[];
    $sql = "
        SELECT
            cohortid
        FROM
            {cohort_members}
        WHERE
            cohortid = :cohort_id AND userid = :id_user
    ";
    try {
        $params = [
            'cohort_id' => $cohort_id,
            'id_user' => $id_user
        ];
        $result = $DB->get_record_sql($sql, $params);
        return $result;
    } catch (dml_exception $e) {
        error_log($e->getMessage());
        return false;
    }
}
function user_in_lp_template($id_user, $lp_template_id) {
    global $DB;
    $result=[];
    $sql = "
        SELECT
            id
        FROM
            {competency_plan}
        WHERE
            templateid = :lp_template_id AND userid = :id_user
    ";
    try {
        $params = [
            'lp_template_id' => $lp_template_id,
            'id_user' => $id_user
        ];
        $result = $DB->get_record_sql($sql, $params);
        return $result;
    } catch (dml_exception $e) {
        error_log($e->getMessage());
        return false;
    }
}
function name_template_lp($lp_id) {
    global $DB;
    $result=[];
    $sql = "
        SELECT
            shortname
        FROM
            {competency_template}
        WHERE
            id = :lp_id 
    ";
    try {
        $params = [
            'lp_id' => $lp_id,
        ];
        $result = $DB->get_record_sql($sql, $params);
        return $result;
    } catch (dml_exception $e) {
        error_log($e->getMessage());
        return false;
    }
}
function load_learning_plans_for_circles($id_user, $circle_num) {
    global $DB;
    $sql = "
    SELECT DISTINCT
        ct.id AS id_learning_plans, -- ID of the learning plan
        ct.shortname,              -- Short name of the learning plan
        ctc.templateid,            -- Template ID associated with the competency
        ctc.id,                    -- ID of the competency-template relationship
        cth.cohortid,              -- Cohort ID associated with the learning plan
        c.name                     -- Name of the cohort
    FROM
        {competency_template} ct -- Table for competency templates
    JOIN {competency_templatecomp} ctc ON ctc.templateid = ct.id -- Join with competency-template relationship
    JOIN {competency_templatecohort} cth ON cth.templateid = ct.id -- Join with template-cohort relationship
    JOIN {cohort} c ON c.id = cth.cohortid -- Join with cohort table
    join {user} u on u.id=$id_user
    WHERE
        ct.shortname LIKE '$circle_num%' -- Filter for learning plans with shortname starting with '3'
        AND ct.visible = 1;    -- Ensure the learning plan is visible
";
    try {
        $params = [
            'cohort_id' => $circle_num,
            'id_user' => $id_user
        ];
        $result = $DB->get_records_sql($sql, $params);
        return $result;
    } catch (dml_exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

function get_lang_x_user($id_user) {
    global $DB;
    try {
        $lang_for_user_x = $DB->get_field('user', 'lang', ['id' => $id_user]);
        return $lang_for_user_x ?: null;
    } catch (dml_exception $e) {
        error_log("Error retrieving user language: " . $e->getMessage());
        return null;
    }
}
function get_cohort($name_cohort,$user_id){
    global $DB;
    $sql="
        SELECT DISTINCT
            c.id,
            c.name,
            c.idnumber,
            CASE 
                WHEN cm.cohortid IS NOT NULL THEN 1
                ELSE 0
            END AS matriculado
        FROM mdl_cohort c
        LEFT JOIN mdl_cohort_members cm 
            ON cm.cohortid = c.id 
            AND cm.userid = $user_id
        WHERE c.name LIKE '".$name_cohort."%';
    ";
    try {
        $result = $DB->get_records_sql($sql);
        return $result;
    } catch (dml_exception $e) {
        error_log($e->getMessage());
        return false;
    }
}
?>

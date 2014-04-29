<?php

add_shortcode('studienangebot', 'studienangebot');

function studienangebot($atts) {

    if (!class_exists('FAU_Studienangebot'))
        return 'Die klasse FAU_Studienangebot ist nicht vorhanden.';

    $abschlussgruppe = FAU_Studienangebot::get_abschlussgruppe();
    $the_permalink = get_permalink();


    ob_start();
    ?>

	<div class="row">
	<div class="span3">
    <style>
        #studienangebot label { 
            float: none !important; 
            display: inline !important;
        } 
        #studienangebot br { 
            display: none;
        }
    </style>
    <form id="studienangebot" action="<?php $the_permalink; ?>" method="get">
        <input type="hidden" name="sasu" value="1">

        <h3>Studiengang</h3>
        <?php $terms = get_terms('studiengang', array('pad_counts' => true, 'hide_empty' => 1)); ?>
        <p>
            <select name="sasg" id="studiengang_category">
                <option value="0">Alle Studiengänge</option>
                <?php foreach ($terms as $term): ?>
                    <option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <h3>Fächergruppe</h3>
        <?php $terms = get_terms('faechergruppe', array('pad_counts' => true, 'hide_empty' => 1)); ?>
        <?php foreach ($terms as $term): ?>
            <p>
                <input type="checkbox" name="safg<?php echo $term->term_id; ?>" value="1" id="faechergruppe-<?php echo $term->term_id; ?>">
                <label for="faechergruppe-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></label>
            </p>
        <?php endforeach; ?>
        <h3>Abschluss</h3>

        <?php foreach ($abschluss as $key => $terms): ?>
            <h4><?php echo $abschlussgruppe[$key]; ?></h4>
            <?php foreach ($terms as $term): ?>
                <p>
                    <input type="checkbox" name="saab<?php echo $term->term_id; ?>" value="1" id="abschluss-<?php echo $term->term_id; ?>">
                    <label for="abschluss-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></label>
                </p>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <h3>Studienbeginn</h3>
        <?php $terms = get_terms('semester', array('pad_counts' => true, 'hide_empty' => 1)); ?>
        <?php foreach ($terms as $term): ?>
            <p>
                <input type="checkbox" name="sase<?php echo $term->term_id; ?>" value="1" id="semester-<?php echo $term->term_id; ?>">
                <label for="semester-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></label>
            </p>
        <?php endforeach; ?>
        <h3>Studienort></h3>
        <?php $terms = get_terms('studienort', array('pad_counts' => true, 'hide_empty' => 1)); ?>
        <?php foreach ($terms as $term): ?>
            <p>
                <input type="checkbox" name="saso<?php echo $term->term_id; ?>" value="1" id="studienort-<?php echo $term->term_id; ?>">
                <label for="studienort-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></label>
            </p>
        <?php endforeach; ?>
        <h3>Weitere Eigenschaften</h3>
        <?php $terms = get_terms('saattribut', array('pad_counts' => true, 'hide_empty' => 1)); ?>
        <?php foreach ($terms as $term): ?>
            <p>
                <input type="checkbox" name="saat<?php echo $term->term_id; ?>" value="1" id="saattribut-<?php echo $term->term_id; ?>">
                <label for="saattribut-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></label>
            </p>
        <?php endforeach; ?>                
        <p>
            <input type="submit" value="Auswählen">
        </p>
    </form>
</div>

    <?php
    $out = ob_get_clean();
	echo $out;
	
	echo '<div class="span9">';

	if (( $_SERVER['REQUEST_METHOD'] == 'GET' ) && isset($_GET['said'])) {

        $post_id = (int) $_GET['said'];
        $post = get_post($post_id);

        if ($post && $post->post_type = 'studienangebot') {

            $terms = wp_get_object_terms($post_id, array('studiengang', 'abschluss', 'faechergruppe', 'fakultaet', 'semester', 'studienort'));

            $faechergruppe = array();
            $abschluss = array();
            $semester = array();
            $studienort = array();

            foreach ($terms as $term) {

                $term_link = add_query_arg('sasu', 1, $the_permalink);
                $term_link = add_query_arg('sasg', 0, $term_link);


                if ($term->taxonomy == 'faechergruppe') {
                    $term_link = add_query_arg('safg' . $term->term_id, 1, $term_link);
                    $faechergruppe[] = '<a href="' . $term_link . '">' . $term->name . '</a>';
                }

                if ($term->taxonomy == 'fakultaet') {
                    $term_link = add_query_arg('safg' . $term->term_id, 1, $term_link);
                    $fakultaet[] = '<a href="' . $term_link . '">' . $term->name . '</a>';
                } elseif ($term->taxonomy == 'abschluss') {
                    $term_link = add_query_arg('saab' . $term->term_id, 1, $term_link);
                    $abschluss[] = '<a href="' . $term_link . '">' . $term->name . '</a>';
                } elseif ($term->taxonomy == 'semester') {
                    $term_link = add_query_arg('sase' . $term->term_id, 1, $term_link);
                    $semester[] = '<a href="' . $term_link . '">' . $term->name . '</a>';
                } elseif ($term->taxonomy == 'studienort') {
                    $term_link = add_query_arg('saso' . $term->term_id, 1, $term_link);
                    $studienort[] = '<a href="' . $term_link . '">' . $term->name . '</a>';
                }
            }

            $faechergruppe = isset($faechergruppe) ? implode(', ', $faechergruppe) : '';
            $fakultaet = isset($fakultaet) ? implode(', ', $fakultaet) : '';
            $abschluss = isset($abschluss) ? implode(', ', $abschluss) : '';
            $semester = isset($semester) ? implode(', ', $semester) : '';
            $studienort = isset($studienort) ? implode(', ', $studienort) : '';

            $regelstudienzeit = get_post_meta( $post_id, 'sa_regelstudienzeit', true );
            $kizs_pdf = get_post_meta( $post_id, 'sa_pdf', true );
            
            $schwerpunkte = get_post_meta( $post_id, 'sa_schwerpunkte', true );
            $sprachkenntnisse = get_post_meta( $post_id, 'sa_sprachkenntnisse', true );
            
            $linktext_deutsch = get_post_meta( $post_id, 'sa_linktext_deutsch', true );
            $link_deutsch = get_post_meta( $post_id, 'sa_link_deutsch', true );
            $deutschkenntnisse = '<a href="' . $link_deutsch . '">' . $linktext_deutsch . '</a>';

            $linktext_pruefungsordnung = get_post_meta( $post_id, 'sa_linktext_pruefungsordnung', true );
            $link_pruefungsordnung = get_post_meta( $post_id, 'sa_link_pruefungsordnung', true );
            $pruefungsordnung = '<a href="' . $link_pruefungsordnung . '">' . $linktext_pruefungsordnung . '</a>';
            
            $linktext_pruefungsamt = get_post_meta( $post_id, 'sa_linktext_pruefungsamt', true );
            $link_pruefungsamt = get_post_meta( $post_id, 'sa_link_pruefungsamt', true );
            $pruefungsamt = '<a href="' . $link_pruefungsamt . '">' . $linktext_pruefungsamt . '</a>';
            
            $besondere_hinweise = get_post_meta( $post_id, 'sa_besondere_hinweise', true );
            
            $linktext_fach = get_post_meta( $post_id, 'sa_linktext_fach', true );
            $link_fach = get_post_meta( $post_id, 'sa_link_fach', true );
            $fach = '<a href="' . $link_fach . '">' . $linktext_fach . '</a>';
            
            $linktext_ssc = get_post_meta( $post_id, 'sa_linktext_ssc', true );
            $link_ssc = get_post_meta( $post_id, 'sa_link_ssc', true );
            $ssc = '<a href="' . $link_ssc . '">' . $linktext_ssc . '</a>';
 
            $linktext_einfuehrung = get_post_meta( $post_id, 'sa_linktext_einfuehrung', true );
            $link_einfuehrung = get_post_meta( $post_id, 'sa_link_einfuehrung', true );
            $einfuehrung = '<a href="' . $link_einfuehrung . '">' . $linktext_einfuehrung . '</a>';
             
            $constant_slug = array(
                'studienberatung-allgemein',
                'studentenvertretungfachschaft',
                'semester-und-terminplan',
                'hinweisblatt-zur-einschreibung',
                'semesterbeitraege',
                'berufl-moeglichkeiten',
                'studienanfaenger-zulasungsfrei',
                'studienanfaenger-nc',
                'studienanfaenger-zvs',                
                'hoeheres-semester-zulasungsfrei',
                'hoeheres-semester-nc',
                'hoeheres-semester-zvs'
            );
            
            $constant = array();
            foreach($constant_slug as $slug) {
                $constant[$slug] = array();
                $term = get_term_by('slug', $slug, 'saconstant');
                if($term) {
                    $t_id = $term->term_id;
                    $name = $term->name;
                    $meta = get_option("saconstant_category_$t_id");                    
                    if(!empty($meta['linktext']) && !empty($meta['linkurl'])) {
                        $constant[$slug] = array(
                            'label' => $name, 
                            'link' => sprintf('<a href="%2$s">%1$s</a>', $meta['linktext'], $meta['linkurl'])
                            );
                    } elseif(!empty($meta['linktext'])) {
                        $constant[$slug] = array(
                            'label' => $name, 
                            'link' => $meta['linktext']
                            );
                    } else {
                        $constant[$slug] = array(
                            'label' => $name, 
                            'link' => ''
                            );
                    }

                } 
            }
            
            $anfaenger = array();
            if(get_post_meta( $post_id, 'sa_anfaenger_zulassungsfrei', true ))
                $anfaenger[] = $constant['studienanfaenger-zulasungsfrei']['link'];
            
            if(get_post_meta( $post_id, 'sa_anfaenger_nc', true ))
                $anfaenger[] = $constant['studienanfaenger-nc']['link'];

            if(get_post_meta( $post_id, 'sa_anfaenger_zvs', true ))
                $anfaenger[] = $constant['studienanfaenger-zvs']['link'];
            
            $anfaenger = !empty($anfaenger) ? implode(', ', $anfaenger) : '';

            $hoeheres_semester = array();
            if(get_post_meta( $post_id, 'sa_hoeheres_semester_zulassungsfrei', true ))
                $hoeheres_semester[] = $constant['hoeheres-semester-zulasungsfrei']['link'];
            
            if(get_post_meta( $post_id, 'sa_hoeheres_semester_nc', true ))
                $hoeheres_semester[] = $constant['hoeheres-semester-nc']['link'];

            if(get_post_meta( $post_id, 'sa_hoeheres_semester_zvs', true ))
                $hoeheres_semester[] = $constant['hoeheres-semester-zvs']['link'];

            $hoeheres_semester = !empty($hoeheres_semester) ? implode(', ', $hoeheres_semester) : '';
            
            echo '<h3>' . esc_html($post->post_title) . '</h3>';

            echo '<table>';
            echo '<tbody>';

            echo '<tr><td>Fächergruppe</td><td>' . $faechergruppe . '</td></tr>';
            echo '<tr><td>Fakultät</td><td>' . $fakultaet . '</td></tr>';
            echo '<tr><td>Abschluss</td><td>' . $abschluss . '</td></tr>';
            echo '<tr><td>Regelstudienzeit</td><td>' . $regelstudienzeit . '</td></tr>';
            echo '<tr><td>Studienbeginn</td><td>' . $semester . '</td></tr>';
            echo '<tr><td>Studienort</td><td>' . $studienort . '</td></tr>';
            echo '<tr><td>Kurzinformationen zum Studiengang</td><td><a href="' . $kizs_pdf . '">PDF</a></td></tr>';
            echo '<tr><td colspan="2">Zugangsvoraussetzungen</td></tr>';
            echo '<tr><td style="padding-left: 2em">für Studienanfänger</td><td>' . $anfaenger . '</td></tr>';
            echo '<tr><td style="padding-left: 2em">höheres Semester</td><td>' . $hoeheres_semester . '</td></tr>';
            echo '<tr><td style="padding-left: 2em">Bewerbungsverfahren</td><td></td></tr>';
            echo '<tr><td>Kombination</td><td></td></tr>';
            echo '<tr><td>Studienrichtungen/ -schwerpunkte/ -inhalte</td><td>' . $schwerpunkte . '</td></tr>';
            echo '<tr><td>Sprachkenntnisse</td><td>' . $sprachkenntnisse . '</td></tr>';
            echo '<tr><td>Deutschkenntnisse für ausländische Studierende</td><td>' . $deutschkenntnisse . '</td></tr>';
            echo '<tr><td>Studien-und Prüfungsordnung mit Studienplan</td><td>' . $pruefungsordnung . '</td></tr>';
            echo '<tr><td>Prüfungsamt/Prüfungsbeauftragte</td><td>' . $pruefungsamt . '</td></tr>';
            echo '<tr><td>Besondere Hinweise</td><td>' . $besondere_hinweise . '</td></tr>';
            echo '<tr><td>Link zum Fach</td><td>' . $fach . '</td></tr>';
            echo '<tr><td colspan="2">Studienberatung</td></tr>';
            echo '<tr><td style="padding-left: 2em">' . $constant['studienberatung-allgemein']['label'] . '</td><td>' . $constant['studienberatung-allgemein']['link'] . '</td></tr>';
            echo '<tr><td style="padding-left: 2em">Studien-Service-Center</td><td>' . $ssc . '</td></tr>';
            echo '<tr><td>Einführungsveranstaltungen für Studienanfänger /Vorkurse</td><td>' . $einfuehrung . '</td></tr>';
            echo '<tr><td>' . $constant['studentenvertretungfachschaft']['label'] . '</td><td>' . $constant['studentenvertretungfachschaft']['link'] . '</td></tr>';
            echo '<tr><td>' . $constant['semester-und-terminplan']['label'] . '</td><td>' . $constant['semester-und-terminplan']['link'] . '</td></tr>';
            echo '<tr><td>' . $constant['hinweisblatt-zur-einschreibung']['label'] . '</td><td>' . $constant['hinweisblatt-zur-einschreibung']['link'] . '</td></tr>';
            echo '<tr><td>' . $constant['semesterbeitraege']['label'] . '</td><td>' . $constant['semesterbeitraege']['link'] . '</td></tr>';
            echo '<tr><td>' . $constant['berufl-moeglichkeiten']['label'] . '</td><td>' . $constant['berufl-moeglichkeiten']['link'] . '</td></tr>';

            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Es konnte nichts gefunden werden.</p>';
        }

        //return;
        
    } elseif (( $_SERVER['REQUEST_METHOD'] == 'GET' ) && isset($_GET['sasu'])) {

        $categories = array();

        foreach ($_GET as $key => $value) {
            if (empty($value))
                continue;

            $var = substr($key, 0, 4);
            $val = substr($key, 4);

            if ($key == 'sasg')
                $categories['studiengang'][] = (int) $value;

            elseif ($var == 'safg')
                $categories['faechergruppe'][] = (int) $val;

            elseif ($var == 'saab')
                $categories['abschluss'][] = (int) $val;

            elseif ($var == 'sase')
                $categories['semester'][] = (int) $val;

            elseif ($var == 'saso')
                $categories['studienort'][] = (int) $val;

            elseif ($var == 'saat')
                $categories['saattribut'][] = (int) $val;
        }

        $tax_query = array();

        if (!empty($categories)) {

            $tax_query['relation'] = 'AND';

            foreach ($categories as $key => $value) {

                $tax_query[] = array(
                    'taxonomy' => $key,
                    'terms' => $value
                );
            }
        }

        $args = array(
            'nopaging' => true,
            'post_type' => 'studienangebot',
            'tax_query' => $tax_query
        );

        $the_query = new WP_Query($args);

        //echo '<p><a href="' . $the_permalink . '">Zurück zur Auswahl</a></p>';

        if ($the_query->have_posts()) {

            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Studiengang</th>', '<th>Abschluss</th>', '<th>Studienbeginn</th>', '<th>Ort</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($the_query->have_posts()) {

                $the_query->the_post();

                $terms = wp_get_object_terms(get_the_ID(), array('studiengang', 'abschluss', 'faechergruppe', 'semester', 'studienort'));
                $studiengang = array();
                $abschluss = array();
                $semester = array();
                $studienort = array();

                foreach ($terms as $term) {

                    $term_link = add_query_arg('sasu', 1, $the_permalink);
                    $term_link = add_query_arg('sasg', 0, $term_link);


                    if ($term->taxonomy == 'studiengang') {
                        $term_link = add_query_arg('said', get_the_ID(), $the_permalink);
                        $studiengang[] = '<a href="' . $term_link . '">' . $term->name . '</a>';
                    } elseif ($term->taxonomy == 'abschluss') {
                        $term_link = add_query_arg('saab' . $term->term_id, 1, $term_link);
                        $abschluss[] = '<a href="' . $term_link . '">' . $term->name . '</a>';
                    } elseif ($term->taxonomy == 'semester') {
                        $term_link = add_query_arg('sase' . $term->term_id, 1, $term_link);
                        $semester[] = '<a href="' . $term_link . '">' . $term->name . '</a>';
                    } elseif ($term->taxonomy == 'studienort') {
                        $term_link = add_query_arg('saso' . $term->term_id, 1, $term_link);
                        $studienort[] = '<a href="' . $term_link . '">' . $term->name . '</a>';
                    }
                }

                $studiengang = isset($studiengang) ? implode(', ', $studiengang) : '';
                $abschluss = isset($abschluss) ? implode(', ', $abschluss) : '';
                $semester = isset($semester) ? implode(', ', $semester) : '';
                $studienort = isset($studienort) ? implode(', ', $studienort) : '';

                echo '<tr>';
                echo '<td>' . $studiengang . '</td>',
                '<td>' . $abschluss . '</td>',
                '<td>' . $semester . '</td>',
                '<td>' . $studienort . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            //echo '<p><a href="' . $the_permalink . '">Zurück zur Auswahl</a></p>';
        } else {
            echo '<p>Es konnte nichts gefunden werden.</p>';
        }

        wp_reset_postdata();

        //return;
    }

    $terms = get_terms('abschluss', array('pad_counts' => true, 'hide_empty' => 1));
    $abschluesse = array();
    foreach ($terms as $term) {
        $term_meta = get_option("abschluss_category_{$term->term_id}");
        if ($term_meta && !empty($abschlussgruppe[$term_meta['abschlussgruppe']]))
            $abschluesse[$term_meta['abschlussgruppe']][$term->term_id] = (object) array(
                        'term_id' => $term->term_id,
                        'name' => $term->name,
            );
    }

    uksort($abschluesse, 'strnatcasecmp');

    $abschluss = array();
    foreach ($abschlussgruppe as $key => $val) {
        if (isset($abschluesse[$key]))
            $abschluss[$key] = $abschluesse[$key];
    }

	echo '</div></div>';
	return;
}

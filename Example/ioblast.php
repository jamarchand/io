
<?php
    //Assigning locus: Using the protein list to asign an ordered locus to each protein. (Note: Locus assignment is an approximation. Fully assembled genomes will have only errors at terminal loci, while partially assembled genomes will have errors between non-overlapping contigs.
    function find_locus($fname, $protM){
        $i = 0;
        $handle = fopen($fname, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $exprot = explode('WP',$line);
                $prot = 'WP'.substr($exprot[1],0,10);
                $i = $i + 1;
                if ($protM == $prot){
                    return $i;
                    break;
                }
            }
            fclose($handle);
        } else {
            print_r("error opening the file.");
        }
    }
    
    
    ///Matching: Trimming the blast results to only include WP accession for each protein and finding the locus for each alignment.
    function match_prot($fname, $dl){
        $i = 0;
        $handle = fopen($fname, "r");
        $assigned= fopen("locus.csv", "w");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                
                //Find protA
                $exprot = explode('WP',$line);
                if (sizeof($exprot)==3){ //There should be exactly 3 lines for 2 proteins
                    $st = strpos($line, $dl);
                    
                    $protA = 'WP'.substr($exprot[1],0,10); //Get protein A
                    $locusA = find_locus('tables/cattleya-table.txt',$protA); //Get protein locus
                    $protB = 'WP'.substr($exprot[2],0,10); //Get protein B
                    $locusB = find_locus('tables/catenulae-table.txt',$protB); //Get protein B locus

                    $protloc = $protA.','.$protB.','.$locusA.','.$locusB."\n";
                    fwrite($assigned, $protloc);

                    print_r($protA.' '.$protB.' '.$locusA.' '.$locusB);
                    print_r("\n");
                }
            }
            fclose($handle);
        } else {
            print_r("error opening the file.");
        }
        fclose($assigned);
    }
    
    
    
    ///Removing duplicates and order list
    function remove_dup($fname){
        $csv = array_map('str_getcsv', file($fname));
        $deduped= fopen("unique-locus.csv", "w");
        for ($i = 0; $i<10000; $i ++){
            for ($ii = 0; $ii<10000; $ii ++){
                if ($csv[$ii][2] == $i ){
                    $protloc = $csv[$ii][0].','.$csv[$ii][1].','.$csv[$ii][2].','.$csv[$ii][3]."\n";
                    fwrite($deduped, $protloc);
                    break;
                    }
            }
            }
        fclose($deduped);
    }
    
    
    
    ///Clustering dataset based on pairwise distance
    function cluster_set($fname, $dist, $thr){
        $csv = array_map('str_getcsv', file($fname));
        $clusters = fopen("clusters-untrimmed.csv", "w");
        for ($i = 0; $i<=(sizeof($csv)-$dist); $i ++){

            for ($ii = 1; $ii<=$dist; $ii ++){
                $dx = $csv[$i][3]-$csv[$i+$ii][3]; //Calculate distance between proteins
                if (abs($dx)<$thr){ //Threshold based on distance and generate cluster file
                    $pwdist = $csv[$i][0].','.$csv[$i+$ii][0].','.$dx."\n";
                    fwrite($clusters, $pwdist);
                }
            }
        }
        fclose($clusters);
    }
    
    
    ////Local database: Making local blast database of the in-group.
    print_r('Status: Generating in-group database'); print_r("\n");
    shell_exec("makeblastdb -in genomes/catenulae.fasta -out databases/databaseBLAST -dbtype prot -parse_seqids");
    
    
    //Blasting ingroup: Blasting database against query organism (ex: S. cattleya)
    print_r('Status: Blasting to generate ingroup'); print_r("\n");
    shell_exec("blastp -query genomes/cattleya.fasta -db databases/databaseBLAST -outfmt 6 -num_alignments 1 -out ingroup.txt");
    
    
    print_r('Status: Matching sets and assigning loci'); print_r("\n");
    match_prot('ingroup.txt','_prot'); //Delimiter is specific to S. cattleya. Chose most appropriate here.
    
    print_r('Status: Trimming duplicate and ordering set'); print_r("\n");
    remove_dup('locus.csv'); //Remove duplicates.
    
    print_r('Status: Clustering based on distance threashold'); print_r("\n");
    cluster_set('unique-locus.csv', 2, 10);
    
    
    ?>

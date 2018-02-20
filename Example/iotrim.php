
<?php
    
    
    ///Trim dataset, only keep clusters where in-group target matched highest
    function trim_cluster($fname, $protM){
        $csv = array_map('str_getcsv', file($fname));
        $clusters = fopen("clusters-trimmed.csv", "a");
        //print_r($csv);
        for ($i = 0; $i<=(sizeof($csv)); $i ++){
            if ($csv[$i][0]==$protM){
                $clus = $csv[$i][0].','.$csv[$i][1].','.$csv[$i][2]."\n";
                fputcsv($clusters, $csv[$i]);
            }
        }
        //fwrite($clusters, $csv);
        fclose($clusters);
    }
    
    
    
    ///Searching best match of outgroup against ingroup
    function find_prot($fname, $protM){
        $out = 0;
        $handle = fopen($fname, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $exprot = explode('WP',$line);
                
                if (sizeof($exprot)==3){ //There should be exactly 3 lines for 2 proteins
                    $st = strpos($line, $dl);
                    $protA = 'WP'.substr($exprot[1],0,10); //Get protein B
                    $protB = 'WP'.substr($exprot[2],0,10); //Get protein B
                    if ($protM == $protB){
                        $out = 1;
                    }
                }
            }
            fclose($handle);
        }
        return $out;
    }
    
    
    ///Matching: Trimming the blast results to only include WP accession for each protein and finding the locus for each alignment.
    function match_prot($fname, $dl){
        $handle = fopen($fname, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                //Find protA
                $exprot = explode('WP',$line);
                $nxprot = explode('NP',$line);

                if (sizeof($exprot)==3){ //There should be exactly 3 lines for 2 proteins
                    $st = strpos($line, $dl);
                    $protA = 'WP'.substr($exprot[1],0,10); //Get protein A
                    $protB = 'WP'.substr($exprot[2],0,10); //Get protein B
                }
                elseif (sizeof($nxprot)==2){
                    $protA = 'WP'.substr($exprot[1],0,10); //Get protein A
                    $protB = 'NP'.substr($nxprot[1],0,10); //Get protein B
                }
                
                //Search for protein B ingroup
                $ans = find_prot('ingroup.txt',$protB);
                //print_r($ans);
                
                
                //Keep if protein is found in cluster
                if ($ans ==1){
                    trim_cluster('clusters-untrimmed.csv',$protA);
                }
                //What needs work: The trimming aspect needs to be redone atm...
                

            }
            fclose($handle);
        } else {
            print_r("error opening the file.");
        }
        fclose($assigned);
    }
    

    ////Local database: Making local blast database of the out-group. Note that in-group organism should be included in the outgroup. This is used to directly compare which organism is the best hit.
    print_r('Status: Generating out-group database'); print_r("\n");
    shell_exec("makeblastdb -in genomes/outgroup.fasta -out databases/databaseOUT -dbtype prot -parse_seqids");
    
    
    //Blasting outgroup: Blasting database against query organism (ex: S. cattleya)
    print_r('Status: Blasting to generate outgroup'); print_r("\n");
    shell_exec("blastp -query genomes/cattleya.fasta -db databases/databaseOUT -outfmt 6 -num_alignments 1 -out outgroup.txt");
    

    print_r('Status: Trimming dadtaset'); print_r("\n");
    match_prot('outgroup.txt','_prot'); //Trims data set, only keeping clusters where in-group matched highest
    
    print_r('Status: Finished'); print_r("\n");
    ?>

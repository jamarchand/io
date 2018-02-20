Comparative genomics proof-of-concept developed by Jorge Marchand
This software package aided in the discovery of terminal-alkyne amino acid found in Streptomyces spp. 

============================= From =============================

(Discovery of a pathway for terminal-alkyne amino acid biosynthesis) 
Authors:  J. A. Marchand1, M. E. Neugebauer1, C. Lin2, J. Pelton3, M. C. Y. Chang2,4*
Affiliations:1Department of Chemical and Biomolecular Engineering, University of California, Berkeley, CA 94720-14602Department of Chemistry, University of California, Berkeley, CA 94720-14603QB3 Institute, University of California, Berkeley, CA 94720, USA 4Department of Molecular and Cell Biology, University of California, Berkeley, CA 94720-3200
============================= ioBlast =============================
ioBlast concept usage below. This set of software is not optimized, and meant to be used as a learning tool.
Please read through code carefully to make necessary adjustments for personal case use. Example provided 
should be able to replicate results shown in “Discovery of a pathway for terminal-alkyne amino acid biosynthesis”


ioBlast.php takes 6 inputs: 
-Target organism proteome fasta file
-Query organism proteome fasta file
-Ordered set of protein loci for target organism
-Ordered set of protein loci for query organism
-Loci pairwise distance to calculate 
-Clustering distance threshold 


ioBlast.php generates many temporary files but has one main output: 
-set of clustered proteins shared between two organisms (cytoscape ready .csv file) 

============================= ioTrim =============================
ioTrim is used to trim output file from ioBlast to remove clusters that are shared by your target organisms 
and a database of outgroup organisms. The more outgroup organisms you chose, the more possible candidate clusters 
you remove from cluster-space. However, using organisms in the out-group that contain a cluster of interest will result in 
trimming of that cluster from the cluster-space (giving a false negative). Careful attention should be paid towards which 
organisms are used for in-group and which are used for out-group. 


ioTrim.php takes 1 input: 
-Untrimmed .csv file output from ioBlast.php 

ioTrim.php has 1 output: 
-Trimmed .csv file (cityscape ready) 


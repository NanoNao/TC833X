<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('tdf/home.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }



    /**
     * @Route("/member/view", name="viewmember")
     */
    public function viewmemberAction(Request $request)
    {
        $twigParams= array();
        $members = file("../txt/members.txt",FILE_IGNORE_NEW_LINES);
        $ranMember=array();
        $membersexp = array();
        $Name=array("length"=>-1);
        $year=array("year"=>-1);
        $rCountry=array();
        $rBrand=array();
        $rVictory="";
        $averageYear=0;
        $memberCount=0;
        $randomArray=array();
        foreach ($members as $member){
            $explode = explode("#",$member);
            $membersName = $explode[0];
            $membersYear = $explode[1];
            $membersCountry = $explode[2];
            $memberBikeBrand = $explode[3];
            $memberVictory = $explode[4];
            $averageYear += $membersYear;
            $memberCount++;
            if(isset($rCountry[$membersCountry])){
                $rCountry[$membersCountry]++;
            }else{
                $rCountry[$membersCountry]=1;
            }

            if(isset($rBrand[$memberBikeBrand])){
                $rBrand[$memberBikeBrand]++;
            }else{
                $rBrand[$memberBikeBrand]=1;
            }
            if(mb_strlen(str_replace(" ","",$membersName))>$Name["length"]){             //Logest first name
                $Name["length"]=mb_strlen(str_replace(" ","",$membersName));
                $Name["name"]=$membersName;
            }
            if($membersYear>$year["year"]){                                 //Oldest runer
                $year["year"]=$membersYear;
                $year["name"]=$membersName;
            }
            if($memberVictory==0){
                $rVictory .="$membersName";
                if($member != end($members)){
                    $rVictory.=" - ";
                }
            }
            $membersexp[]=array("Name"=>$membersName,"Year"=>$membersYear,"Country"=>$membersCountry,"Brand"=>$memberBikeBrand,"Victory"=>$memberVictory);

        }
        $averageYear=$averageYear/$memberCount;
        $country=array("value"=>-1);
        foreach($rCountry as $key => $value ){
            if($value>$country["value"]){
                $country["value"]=$value;
                $country["name"]=$key;
            }
        }
        $brand="";
        foreach($rBrand as $key => $value ){
            if($value>=2){
                $brand .= $key." (".$value.")";
                if($value != end($rBrand)){
                    $brand.=" - ";
                }
            }
        }
        for ($i=0;$i<20;$i++){
            shuffle($members);
            $member=$members[0];
            $explode = explode("#",$member);
            $membersName = $explode[0];
            $membersYear = $explode[1];
            $membersCountry = $explode[2];
            $memberBikeBrand = $explode[3];
            $memberVictory = $explode[4];
            $ranMember[]=$member;
            $randomArray[]=array("Name"=>$membersName,"Year"=>$membersYear,"Country"=>$membersCountry,"Brand"=>$memberBikeBrand,"Victory"=>$memberVictory);
        }
        $notUnion=array();
        $union =array();
        foreach($members as $key => $member){

            $count=0;
            $membersName = explode("#",$member)[0];
            $membersYear = explode("#",$member)[1];
            $membersCountry = explode("#",$member)[2];
            $memberBikeBrand = explode("#",$member)[3];
            $memberVictory = explode("#",$member)[4];
            $membersSearch=array("Name"=>$membersName,"Year"=>$membersYear,"Country"=>$membersCountry,"Brand"=>$memberBikeBrand,"Victory"=>$memberVictory);
            //var_dump(array_diff($members,$ranMember));
            foreach($randomArray as $ran ){

                if($ran==$membersSearch){
                    $count++;
                }
            }
            if($count==0){      // I make an Array_diff manually but work like var_dump(array_diff($members,$ranMember));
                $notUnion[]=$membersSearch;
            }else{
                $membersSearch["Count"]=$count;
                $union[]=$membersSearch;
            }

        }
        $twigParams["members"]=$membersexp;
        $twigParams["questions"]=array(
            "Name"=>$Name,
            "lastName"=>$Name,
            "oldest"=>$year,
            "country"=>$country,
            "brand"=>$brand,
            "victory"=>$rVictory,
            "average"=>$averageYear

        );
        $twigParams["randoms"]=$randomArray;
        $twigParams["notUnion"]=$notUnion;
        $twigParams["union"]=$union;
        return $this->render('tdf/member.html.twig',$twigParams);
    }
}

//编辑cookies数据
var editData = {
    dkey:'paperstyle',
    //判断当前所选试题是否包含私有试题
    isIncludePrivateTest : function(){
        var list = this.gettestlist();
        for(var i=0; i<list.length; i++){
            if(typeof(list[i][1]) == 'string' && list[i][1].indexOf('c') >= 0){
                return true;
            }
        }
        return false;
    },
    set:function(key,value){
        var tmp_str=localData.get(this.dkey);
        if(!tmp_str){
            localData.set(this.dkey,value);
            return ;
        }

        if(!this.get(key)){
            localData.set(this.dkey,tmp_str+'@#@'+value);
            return;
        }
        var tmp_arr=new Array();
        tmp_arr=tmp_str.split('@#@');
        var tmp_arr_1=new Array();
        for(var tmp_i in tmp_arr){
            tmp_arr_1=tmp_arr[tmp_i].split('@$@');
            if(tmp_arr_1[0]==key){
                tmp_arr[tmp_i]=value;
            }
        }
        localData.set(this.dkey,tmp_arr.join('@#@'));
    },
    del:function(key){
        var tmp_str=localData.get(this.dkey);
        var tmp_arr=new Array();
        tmp_arr=tmp_str.split('@#@');
        var tmp_arr_1=new Array();
        var tmp_arr_2=new Array();
        var j=0;
        for(var tmp_i in tmp_arr){
            tmp_arr_1=tmp_arr[tmp_i].split('@$@');
            if(tmp_arr_1[0]!=key){
                tmp_arr_2[j]=tmp_arr[tmp_i];
                j++;
            }
        }
        localData.set(this.dkey,tmp_arr_2.join('@#@'));
    },
    get:function(key){
        var tmp_str=localData.get(this.dkey);
        if(tmp_str){
            var tmp_arr=new Array();
            tmp_arr=tmp_str.split('@#@');
            var tmp_arr_1=new Array();
            for(var tmp_i in tmp_arr){
                tmp_arr_1=tmp_arr[tmp_i].split('@$@');
                if(tmp_arr_1[0]==key){
                    return tmp_arr_1;
                }
            }
        }
        return false;
    },
    //兼容代码  2015-4-23
    compatibleCode : function(all){
        if(!all || typeof(all) !== 'string'){
            return '';
        }
        var mark = [];
        var arr = all.split('@#@');
        for(var i=0; i<arr.length; i++){
            var iarr = arr[i].split('@$@');
            var test = iarr[5];
            if(test && test != 0 && test.split('|').length == 6){
                mark.push(test);
            }
        }
        for(var i=0; i<mark.length; i++){
            var to = '';
            var match = mark[i].match(/\d{1}$/)[0];
            if(match == 0 || match == 1){
                if(match == 0){
                    to = mark[i].replace(/\|\d{1}$/,'');
                }else if(match == 1){
                    to = 'c'+mark[i].replace(/\|\d{1}$/,'');
                }
                all = all.replace(mark[i],to);
            }
        }
        //当存在不兼容的数据时
        if(mark.length > 0){
            //将兼容后的数据重新保存cookie
            localData.set(this.dkey, all);
        }
        return all;
    },
    getall:function(){
        var all = localData.get(this.dkey);
        return this.compatibleCode(all);
    },
    setall:function(str){
        return localData.set(this.dkey,str);
    },
    clear:function(){
        localData.remove(this.dkey);
    },
    getvalue:function(key,pos){
        var tmp_str=this.get(key);
        if(tmp_str[pos]) return tmp_str[pos];
        return false;
    },
    setvalue:function(key,value,pos){
        var tmp_str_1=this.get(key);
        if(!tmp_str_1 || typeof(tmp_str_1[pos])=="undefined") return false;
        var tmp_str_2=tmp_str_1.join('@$@');
        tmp_str_1[pos]=value;
        var tmp_str_3=tmp_str_1.join('@$@');

        var tmp_str=localData.get(this.dkey);
        tmp_str=tmp_str.replace(tmp_str_2,tmp_str_3);
        localData.set(this.dkey,tmp_str);
    },
    gettitle:function(key){
        var tmp_str=this.get(key);
        if(tmp_str[2]) return tmp_str[2];
        return false;
    },
    settitle:function(key,value){
        var tmp_str_1=this.get(key);
        if(!tmp_str_1 || !tmp_str_1[2]) return false;

        var tmp_str_2=tmp_str_1.join('@$@');
        tmp_str_1[2]=value;
        var tmp_str_3=tmp_str_1.join('@$@');

        var tmp_str=localData.get(this.dkey);
        tmp_str=tmp_str.replace(tmp_str_2,tmp_str_3);
        localData.set(this.dkey,tmp_str);
    },
    getdisplay:function(key){
        var tmp_str=this.get(key);
        if(tmp_str[1]) return tmp_str[1];
        return false;
    },
    setdisplay:function(key,value){
        var tmp_str_1=this.get(key);
        if(value==0 || value==1){
            var tmp_str_2=tmp_str_1.join('@$@');
            tmp_str_1[1]=value;
            var tmp_str_3=tmp_str_1.join('@$@');
            var tmp_str=localData.get(this.dkey);
            tmp_str=tmp_str.replace(tmp_str_2,tmp_str_3);

            localData.set(this.dkey,tmp_str);
        }
    },
    ifhavetest:function(id){
        var tmp_str_1=this.gettypelist(7);
        var tmp_i;
        if(tmp_str_1){
            for(tmp_i in tmp_str_1){
                if(('@$@'+tmp_str_1[tmp_i][5]).indexOf('@$@'+id+'|')!=-1 || tmp_str_1[tmp_i][5].indexOf(';'+id+'|')!=-1){
                    return true;
                }
            }
        }
        return false;
    },
    //获取某条试题的分值
    gettypelistmsg:function(typeid,testid){
        if(typeof(testid)=='undefined' || testid=='') testid=0;
        
        var tmp_str_1=this.gettypelist(7);
        var typemsg='';
        var typetestmsg_1='';
        var typetestmsg="";
        var defchoose=0;//多选题
        var tmp_y;
        var tmp_x;
        var resultarr=new Array();
        var idarr='';
        var tmp_i;
        for(tmp_i in tmp_str_1){
            if(tmp_str_1[tmp_i][0]===typeid){
                typetestmsg_1 = tmp_str_1[tmp_i][5].split(';');
                for(tmp_x in typetestmsg_1){
                    typetestmsg=typetestmsg_1[tmp_x].split('|');
                    //处理分值
                    if(testid==0){
                        if(typeof(resultarr[0])=='undefined') resultarr[0]=typetestmsg[2];
                        else resultarr[0]+=','+typetestmsg[2];
                    }else if(typetestmsg[0]==testid){
                        defchoose=typetestmsg[3];
                        resultarr[0]=typetestmsg[2];
                    }
                }
                if(defchoose==0){
                    idarr+=','+testid;
                }else{
                    for(tmp_y in typetestmsg_1){
                        typetestmsg=typetestmsg_1[tmp_y].split('|');
                        if(typetestmsg[3]==defchoose && typetestmsg[3]!=0){
                            idarr+=','+typetestmsg[0];
                        }
                    }
                }
            }
        }
        resultarr[1]=idarr;
        return resultarr;
    },
    //重置组合某题分值
    edittestscore:function(typeid,testid,rsetscore){
        var tmp_str_1=this.gettypelist(7);
        var tmp_i;
        var oktestmsg_str='';
        for(tmp_i in tmp_str_1){
            if(tmp_str_1[tmp_i][0]==typeid){
                tmp_str_1[tmp_i][5] = tmp_str_1[tmp_i][5].split(';');
                for(var tmp_x in tmp_str_1[tmp_i][5]){
                    tmp_str_1[tmp_i][5][tmp_x]=tmp_str_1[tmp_i][5][tmp_x].split('|');
                    if(tmp_str_1[tmp_i][5][tmp_x][0]==testid){
                        tmp_str_1[tmp_i][5][tmp_x][2]=rsetscore;
                    }
                    tmp_str_1[tmp_i][5][tmp_x]=tmp_str_1[tmp_i][5][tmp_x].join('|');
                }
                oktestmsg_str=tmp_str_1[tmp_i][5].join(';');
            }
        }
        this.setvalue(typeid,oktestmsg_str,5);
    },
    //验证试题分值是否都相等
    checkscore:function(typeid){
        var tmp_str_1=this.get(typeid);
        var tmp_y,tmp_x,tmpFlag,tmpFlagAdd,tmpScoreAdd;
        var tmpArrOne=new Array();
        var tmpArrTwo=new Array();
        var tmpArrThree=new Array();
        var scorearr=new Array();
        var defscore='';
        var arr=new Array();
        var j=0;
        var scoretotal=0;
        tmpFlag='';
        tmpFlagAdd=0;
        tmpScoreAdd=0;
        tmpArrFour=0;
        if(tmp_str_1[5]!=0){
            tmpArrThree = tmp_str_1[5].split(';');//分割题型下的试题
            for(tmp_x in tmpArrThree){
                tmpArrTwo=tmpArrThree[tmp_x].split('|');//获取分数
                if(tmpArrTwo[4]!='0') defscore=-1;
                if(tmpArrTwo[2]!='undefined'){
                    tmpArrOne=tmpArrTwo[2].split(',');//分割分数
                }
                if(defscore=="") defscore=tmpArrOne[0];
                if(tmpFlag==''){
                    tmpFlag=tmpArrTwo[3];
                    if(tmpFlag!='0') tmpFlagAdd=2;
                }else if(tmpFlag!=tmpArrTwo[3] && tmpArrTwo[3]!=0){
                    tmpFlagAdd=2;
                    tmpFlag=tmpArrTwo[3];
                }else if(tmpFlag==tmpArrTwo[3] && tmpFlag!='0'){
                    tmpFlagAdd=1;
                }
                for(tmp_y in tmpArrOne){
                    if(tmpArrOne[tmp_y]!= defscore && defscore!=-1) defscore=-1;
                    scorearr[j]=tmpArrOne[tmp_y];
                    if(tmpFlagAdd==0) scoretotal=scoretotal+parseFloat(tmpArrOne[tmp_y]);
                    else if(tmpFlagAdd==2) tmpScoreAdd=tmpScoreAdd+parseFloat(tmpArrOne[tmp_y]);
                    j++;
                }
                if(tmpFlagAdd==2) scoretotal=scoretotal+tmpScoreAdd*parseInt(tmpArrTwo[4]);
                tmpFlagAdd=0;
                tmpScoreAdd=0;
            }
        }else{
            return '';
        }
        scoretotal=scoretotal.toFixed(1);
        var tmp_score=scoretotal.split('.');
        if(tmp_score[1]=='0') scoretotal=tmp_score[0];
        if(defscore==-1){
            arr[0]=-1;//分值不同
        }else{
            arr[0]=1;//分值相同
        }
        arr[1]=scorearr;
        arr[2]=scoretotal;//试题总分
        return arr;
    },
    //检查是否是选做题
    //返回 [0]:0不是多选题
    //     [1]:多选题ID
    //     [2]:几选几的第二个几
    //     [3]:点击题型，所在多选题的位置
    checkifchoose:function(testid){
        var tmp_i,tmp_j,tmp_k;
        var defchoosenum='';
        var j=0;
        var status,i,k,flag,tmp_str_2;
        var num='';
        var secondnum='';
        var lastresult=new Array();
        var tmp_str_1=this.gettypelist(7);
        flag=0;
        for(tmp_i in tmp_str_1){
            if(flag==1) break;
            if(tmp_str_1[tmp_i][5]!=0){
                tmp_str_2=tmp_str_1[tmp_i][5].split(';');
                for(tmp_j in tmp_str_2){
                    var tmp_str_3=tmp_str_2[tmp_j].split('|');
                    //判断该题是否是多选题，确定多选题的编号
                    if(tmp_str_3[0]==testid){
                        lastresult[1]=new Array();
                        defchoosenum=tmp_str_3[3];//获取多选题的选做编号
                        lastresult[2]=tmp_str_3[4];
                        //重新循环，根据多选题编号返回，该组多选题的所有试题ID
                        if(defchoosenum!='0'){
                            lastresult[0]=1;
                            for(tmp_k in tmp_str_2){
                                tmp_str_3=tmp_str_2[tmp_k].split('|');
                                if(tmp_str_3[3]==defchoosenum){
                                    lastresult[1][j]=tmp_str_3[0];
                                    if(tmp_str_3[0]==testid) lastresult[3]=j;
                                    j++;
                                }
                            }
                        }else{
                            lastresult[0]=0;
                            lastresult[1][0]=testid;
                        }
                        flag==1;
                        break;
                    }
                }
            }
        }
        return lastresult;
    },
    //写入初始化cookie信息
    setCookieInit:function(){
        var juan1=[];
        juan1.push('parthead1@$@1@$@第I卷（选择题）@$@请点击修改第I卷的文字说明');
        var juan2=[];
        juan2.push('parthead2@$@1@$@第II卷（非选择题）@$@请点击修改第II卷的文字说明');
        editData.set(editData.dkey,juan1.join('@#@')+'@#@'+juan2.join('@#@'));
    },
    addtest:function(id,num,style,styleid,testScore,chooseNum,chooseType){
        var tmp_str_1=this.gettypelist(7);
        var tmp_i,tmp_j,tmp_arr1;
        var tmp_str;
        var ifadd=1;
        var score=1;
        var scoreList=''; //分值列表
        var chooseList=''; //分值列表

        if(typeof(testScore)!='undefined' || testScore==''){
            scoreList=testScore;
        }

        if(typeof(chooseNum)=='undefined' || chooseNum=='') chooseNum=0;
        if(typeof(chooseType)=='undefined' || chooseType=='') chooseType=0;
        chooseList='|'+chooseNum+'|'+chooseType;

        if(tmp_str_1){
            for(tmp_i in tmp_str_1){
                if(tmp_str_1[tmp_i][2]==style){
                    if(scoreList==''){
                        tmp_arr1=tmp_str_1[tmp_i][6].split('|');
                        if(tmp_arr1[0]) score=tmp_arr1[0];
                        if(tmp_arr1[1]==2){
                            score=score/num;
                        }
                        score=Math.ceil(score);
                        for(tmp_j=0;tmp_j<num;tmp_j++){
                            scoreList+=','+score;
                        }
                        scoreList=scoreList.substr(1);
                    }

                    if(tmp_str_1[tmp_i][5]==0){
                        tmp_str=id+'|'+num+'|'+scoreList+chooseList;
                    }else{
                        tmp_str=tmp_str_1[tmp_i][5]+';'+id+'|'+num+'|'+scoreList+chooseList;
                    }
                    this.setvalue(tmp_str_1[tmp_i][0],tmp_str,5);
                    ifadd=0;
                    break;
                }
            }
        }
        if(ifadd){
            if(typeof(addTypes)=='undefined' || addTypes==''){
                addTypes = parent.Types;
            }
            if(typeof(subjectID)=='undefined' || subjectID==''){
                subjectID=Cookie.Get("SubjectId");
            }
            var data;
            for(var i in addTypes[subjectID]){
                if(addTypes[subjectID][i]['TypesID']==styleid){
                    data=addTypes[subjectID][i];
                    break;
                }
            }

            if(scoreList==''){
                if(data['DScore']) score=data['DScore'];
                if(data['TypesScore']==2){
                    score=score/num;
                }
                score=Math.ceil(score);
                for(tmp_j=0;tmp_j<num;tmp_j++){
                    scoreList+=','+score;
                }
                scoreList=scoreList.substr(1);
            }

            editData.addtype(data['Volume'],style,'（题型注释）',id+'|'+num+'|'+scoreList+chooseList,data['DScore']+'|'+data['TypesScore']+'|'+data['IfDo']);
        }
        return true;
    },
    addtestbyorder:function(id,num,order){
        var tmp_str_1=this.gettypelist(7);
        var tmp_i,tmp_j,tmp_arr1;
        var tmp_str;
        var jj=0;
        var score=1;
        var scorelist='';
        for(tmp_i in tmp_str_1){
            jj++;
            if(jj==order){
                tmp_arr1=tmp_str_1[tmp_i][6].split('|');
                if(tmp_arr1[0]) score=tmp_arr1[0];
                if(tmp_arr1[1]==2){
                    score=score/num;
                }
                score=Math.ceil(score);
                for(tmp_j=0;tmp_j<num;tmp_j++){
                    scorelist+=','+score;
                }
                scorelist=scorelist.substr(1);
                if(tmp_str_1[tmp_i][5]==0){
                    tmp_str=id+'|'+num+'|'+scorelist;
                }else{
                    tmp_str=tmp_str_1[tmp_i][5]+';'+id+'|'+num+'|'+scorelist;
                }
                this.setvalue(tmp_str_1[tmp_i][0],tmp_str,5);
                break;
            }
        }
    },
    deltest:function(id){
        var tmp_str_1=this.gettypelist(7);
        var tmp_str_2,tmp_str_3,tmp_str_4,tmp_j,tmp_i,tmp_str;
        for(tmp_i in tmp_str_1){
            if(('@$@'+tmp_str_1[tmp_i][5]).indexOf('@$@'+id+'|')!=-1 || tmp_str_1[tmp_i][5].indexOf(';'+id+'|')!=-1){
                tmp_str_2=tmp_str_1[tmp_i][5].split(';');
                tmp_str_4='';
                for(tmp_j in tmp_str_2){
                    tmp_str_3=tmp_str_2[tmp_j].split('|');
                    if(tmp_str_3[0]!=id){
                        tmp_str_4+=';'+tmp_str_2[tmp_j];
                    }
                }
                this.setvalue(tmp_str_1[tmp_i][0],tmp_str_4.substring(1),5);
                return true;
            }
        }
        return false;
    },
    selecttest:function(id){
        //查找试题在哪个名称的题型下
        var tmp_str_1=this.gettypelist(7);
        var tmp_str_2,tmp_str_3;
        var tmp_id='';
        var tmp_i,tmp_j;
        for(tmp_i in tmp_str_1){
            if(tmp_str_1[tmp_i][5]!=0){
                tmp_str_2=tmp_str_1[tmp_i][5].split(';');
                for(tmp_j in tmp_str_2){
                    tmp_str_3=tmp_str_2[tmp_j].split('|');
                    if(tmp_str_3[0]==id) return tmp_str_1[tmp_i][2];
                }
            }
        }
        return false;
    },
    changeTestToTypes:function(testList,typeID){
        if(testList=='' || typeof(testList)=='undefined' || typeID=='' || typeof(typeID)=='undefined') return 0;
        //添加试题到空题型下
        var testCookie=this.getTestCookie(testList,1);
        if(testCookie!=''){
            var tmptest=this.getvalue(typeID,5);
            if(tmptest=='0') this.setvalue(typeID,testCookie,5);
        }
        return 1;
    },
    getTestCookie:function(testList,ifDel){
        //获取test对应cookie数据 ifDel为1则删除对应test数据 注意：试题需要在同一题型下
        if(ifDel=='' || typeof(ifDel)=='undefined') ifDel=0;
        testList=','+testList+',';
        var output='';
        var tmpTest='';
        var flag=0;
        var tmp_str_1=this.gettypelist(7);
        for(tmp_i in tmp_str_1){
            tmpTest='';
            if(tmp_str_1[tmp_i][5]!=0){
                tmp_str_2=tmp_str_1[tmp_i][5].split(';');
                for(tmp_j in tmp_str_2){
                    tmp_str_3=tmp_str_2[tmp_j].split('|');
                    if(testList.indexOf(','+tmp_str_3[0]+',')!=-1){
                        output+=';'+tmp_str_2[tmp_j];
                        flag=1;
                    }else tmpTest+=';'+tmp_str_2[tmp_j];
                }
                if(ifDel==1 && flag==1){
                    if(tmpTest=='') tmp_str_1[tmp_i][5]=0;
                    else tmp_str_1[tmp_i][5]=tmpTest.substr(1);
                    this.setvalue(tmp_str_1[tmp_i][0],tmp_str_1[tmp_i][5],5)
                    return output.substr(1);
                }
            }
        }
    },
    selecttypename:function(style){
        //查找题型名称下的题型id和试题
        var tmp_str_1=this.gettypelist(7);
        var tmp_str_2;
        var tmp_id='';
        var tmp_i,tmp_j;
        for(tmp_i in tmp_str_1){
            if(tmp_str_1[tmp_i][5]!=0){
                if(style==tmp_str_1[tmp_i][2]) return new Array(tmp_str_1[tmp_i][0],tmp_str_1[tmp_i][5]);
            }
        }
        return false;
    },
    addtype:function(juan,title,text,testlist,dscore){
        //在分卷结尾加入题型
        var tmp_str_1=this.getall();
        var lastID=-1;//存分卷所在id
        var lastNum=0;//分卷最后一个id
        var tmp_str,tmp_id,tmp_str_2,tmp_str_3;
        tmp_str_2=tmp_str_1.split('@#@');
        for(var tmp_i in tmp_str_2){
            tmp_str_3=tmp_str_2[tmp_i].split('@$@');
            if(tmp_str_3[0]=='parthead'+juan){
                lastID=tmp_i;
            }
            if(tmp_str_3[0].indexOf('questypehead'+juan+'_')!=-1){
                lastID=tmp_i;
                lastNum=tmp_str_3[0].replace('questypehead'+juan+'_','');
            }
        }
        lastNum=parseInt(lastNum)+1;
        tmp_str='questypehead'+juan+'_'+lastNum+'@$@1@$@'+title+'@$@'+text+'@$@1@$@'+testlist+'@$@'+dscore;
        tmp_str_2[lastID]=tmp_str_2[lastID]+'@#@'+tmp_str;
        this.setall(tmp_str_2.join('@#@'));
        this.resettypeid();
    },
    deltype:function(id){
        this.del(id);
        this.resettypeid();
    },
    changetypes:function(t1,t2){
        var tmp_str_1=this.get(t1);
        var tmp_str_2=this.get(t2);
        this.set(t2,'tmp_nnn@$@1');
        this.set(t1,tmp_str_2.join('@$@'));
        this.set('tmp_nnn',tmp_str_1.join('@$@'));
        this.resettypeid();
    },
    resettypeid:function(){
        var tmp_str=localData.get(this.dkey);
        var tmp_arr=new Array();
        tmp_arr=tmp_str.split('@#@');
        var tmp_arr_1=new Array();
        var tmp_arr_2=new Array();
        var j=0;
        var tmp_i;
        for(var tmp_i in tmp_arr){
            tmp_arr_1=tmp_arr[tmp_i].split('@$@');
            if(tmp_arr_1.length==7){
                tmp_arr_2=tmp_arr_1[0].split('_');
                tmp_arr_1[0]=tmp_arr_2[0]+'_'+j;
                j++;
                tmp_arr[tmp_i]=tmp_arr_1.join('@$@');
            }
            if(tmp_arr_1[0].indexOf('parthead')!=-1){
                j=1;
            }
        }
        localData.set(this.dkey,tmp_arr.join('@#@'));
    },
    //获取当前学科的所有题型名称
    gettypename:function(){
        var tmp_str_1=this.gettypelist(7);
        var typearr=new Array();
        var i=0;
        var tmp_i;
        for (tmp_i in tmp_str_1){
            typearr[tmp_i]=new Array();
            typearr[tmp_i][0]=tmp_str_1[tmp_i][0];
            typearr[tmp_i][1]=tmp_str_1[tmp_i][2];
        }
        return typearr;
    },
    getfenjuan:function(){
        //获取分卷数组
        var tmp_str_1=this.getall();
        if(!tmp_str_1) return false;
        var tmp_str_2,tmp_str_3;
        var output=new Array();
        var k=-1;
        var j=0;
        tmp_str_2=tmp_str_1.split('@#@');
        for(tmp_i in tmp_str_2){
            tmp_str_3=tmp_str_2[tmp_i].split('@$@');
            if(tmp_str_3[0].indexOf('parthead')!=-1){
                k++;
                output[k]='';
                output[k+2]='';
            }
            if(tmp_str_3.length==7){
                output[k]+='|'+tmp_str_3[2];
                output[k+2]+='|'+tmp_str_3[6];
            }
        }
        for($j=0;j<output.length;j++){
            if(output[j]=='') output[j]=new Array();
            else output[j]=output[j].substring(1).split('|');
        }
        return output;
    },
    deltypetest:function(typename){
        //删除题型中的试题
        var tmp_str_1=this.gettypelist(7);
        var tmp_id='';
        var tmp_i;
        for(tmp_i in tmp_str_1){
            if(tmp_str_1[tmp_i][2]==typename){
                tmp_id=tmp_str_1[tmp_i][0];
                break;
            }
        }
        if(tmp_id) this.setvalue(tmp_id,0,5);
    },
    delalltypetest:function(){
        //删除题型中的试题
        var tmp_str_1=this.gettypelist(7);
        var tmp_i;
        for(tmp_i in tmp_str_1){
            if(tmp_str_1[tmp_i][5]!='0'){
                this.setvalue(tmp_str_1[tmp_i][0],0,5);
            }
        }
    },
    gettypelist:function(len){
        //获取某一类型的数据列表
        var output=new Array();
        var tmp_str_3;
        var tmp_str_1=this.getall();
        if(!tmp_str_1) return;
        var tmp_str_2=tmp_str_1.split('@#@');
        var tmp_i;
        var j=0;
        for(tmp_i in tmp_str_2){
            tmp_str_3=tmp_str_2[tmp_i].split('@$@');
            if(tmp_str_3.length==len){
                output[j]=tmp_str_3;
                j++;
            }
        }
        if(j==0) return ;
        return output;
    },
    gettestlist:function(){
        //获取试题数组 最后一位数组为试题总数
        var tmp_str_1=this.getall();
        if(!tmp_str_1) return false;
        var output=new Array();
        var tmp_str_3,tmp_str_4,tmp_str_5,tmp_str_6;
        tmp_str_5=new Array();
        tmp_str_6=new Array();
        var tmp_str_2=tmp_str_1.split('@#@');
        var j=0;
        var k=0;
        var total=0;
        var hangtotal=0;
        var tmp_i,tmp_j;
        for(tmp_i in tmp_str_2){
            tmp_str_3=tmp_str_2[tmp_i].split('@$@');
            if(tmp_str_3.length==7){
                if(tmp_str_3[5]!=0){
                    tmp_str_4=tmp_str_3[5].split(';');
                    k=0;
                    hangtotal=0;
                    for(tmp_j in tmp_str_4){
                        tmp_str_5=tmp_str_4[tmp_j].split('|');
                        //tmp_str_6[k]=new Array(tmp_str_5[0],tmp_str_5[1]);
                        total+=parseInt(tmp_str_5[1]);
                        hangtotal+=parseInt(tmp_str_5[1]);
                        //k++;
                    }
                    output[j]=new Array(tmp_str_3[2],tmp_str_3[5],hangtotal,tmp_str_3[0]);
                }else{
                    output[j]=new Array(tmp_str_3[2],0,0,tmp_str_3[0]);
                }
                j++;
            }else if(tmp_str_3.length==5 && tmp_str_3[0]!='attr'){
                output[j]=new Array(tmp_str_3[2],0,0,tmp_str_3[0]);
                j++;
            }
        }
        output[j]=total;
        return output;
    },
    gettestid:function(){
        var output='';
        var tmp_str_1=this.gettestlist();
        var tmp_str_2,tmp_str_3;
        var len=tmp_str_1.length;
        if(len<=1) return false;
        for(var i=0;i<len-1;i++){
            if(!tmp_str_1[i][2] || typeof(tmp_str_1[i][2])=="undefined") continue;
            if(tmp_str_1[i][2]!=0){
                tmp_str_2=tmp_str_1[i][1].split(';');
                for(var j=0;j<tmp_str_2.length;j++){
                    tmp_str_3=tmp_str_2[j].split('|');
                    output+=','+(tmp_str_3[0]);
                }
            }
        }
        return output.substring(1);
    },
    //添加一个参数，判断多选题内部的移动 t1、t2试题id 把t1移动到t2的up上面 0或下面 1
    changetest:function(t1,t2,up){
        if(t1=='' || typeof(t1)=='undefined' || t2=='' || typeof(t2)=='undefined'){
            return 0;
        }
        if(typeof(up)=='undefined' || up==''){
            up=0
        }
        var tmp_str_1,tmp_str_2;
        var tmp_t1=new Array();
        var tmp_t2=new Array();
        var tmp_str_3=new Array();
        var tmp_str_4=new Array();
        var tmp_i,tmp_j;
        var tmp_str=this.gettypelist(7);
        var sign1=new Array();
        var sign2=new Array();
        var j=0;
        var z=0;
        var k=0;
        var newstr;

        t1=','+t1+',';
        t2=','+t2+',';
        for(tmp_i in tmp_str){
            if(tmp_str[tmp_i][5]=='0') continue;
            tmp_str_1=tmp_str[tmp_i][5].split(';');
            for(tmp_j in tmp_str_1){
                tmp_str_2=tmp_str_1[tmp_j].split('|');
                if(t1.indexOf(','+tmp_str_2[0]+',')!=-1){
                    tmp_t1[j]=tmp_str_1[tmp_j];
                    tmp_str_1[tmp_j]="{#1#}";
                    sign1[j]="{#1#}";
                    tmp_str_3[0]=tmp_str[tmp_i][0];
                    tmp_str_3[1]=tmp_str_1.join(';');
                    j++;
                    if(k==0) k=2;
                }
                if(t2.indexOf(','+tmp_str_2[0]+',')!=-1){
                    tmp_t2[z]=tmp_str_1[tmp_j];
                    tmp_str_1[tmp_j]="{#2#}";
                    sign2[z]="{#2#}";
                    tmp_str_4[0]=tmp_str[tmp_i][0];
                    tmp_str_4[1]=tmp_str_1.join(';');
                    z++;
                    if(k==0) k=1;
                }
            }
        }
        if(k==0) return 0;
        tmp_t1=tmp_t1.join(';');
        tmp_t2=tmp_t2.join(';');
        if(tmp_str_4[0]==tmp_str_3[0]){
            newstr = (k==1 ? tmp_str_3[1] : tmp_str_4[1]);
            newstr=this.replacestr(newstr,sign1.join(';'));
            if(up==0){
                newstr=newstr.replace(sign2.join(';'),tmp_t1+';'+tmp_t2);
            }else{
                newstr=newstr.replace(sign2.join(';'),tmp_t2+';'+tmp_t1);
            }
            if(newstr=='') newstr=0;
            this.setvalue(tmp_str_3[0],newstr,5);
        }else{
            newstr = tmp_str_3[1];
            newstr=this.replacestr(newstr,sign1.join(';'));
            if(newstr=='') newstr=0;
            this.setvalue(tmp_str_3[0],newstr,5);
            newstr = tmp_str_4[1];
            if(up==0){
                newstr=newstr.replace(sign2.join(';'),tmp_t1+';'+tmp_t2);
            }else{
                newstr=newstr.replace(sign2.join(';'),tmp_t2+';'+tmp_t1);
            }
            if(newstr=='') newstr=0;
            this.setvalue(tmp_str_4[0],newstr,5);
        }
        return 1;
    },
    replacestr:function(strn,repn){
        strn=strn.replace(repn,'').replace(';;',';');
        if(strn.substr(0,1)==';') strn=strn.substr(1);
        if(strn.substr(strn.length-1)==';') strn=strn.substr(0,strn.length-1);
        return strn;
    },
    //替换试题 t1试题id t2试题id|小题数
    replacetest:function(t1,t2){
        var tmp_str_1,tmp_str_2,tmp_t1,tmp_t2;
        var tmp_str_3,tmp_str_4;
        var tmp_i,tmp_j,tmp_k;
        var tmp_str=this.gettypelist(7);
        var score=0;
        var tmpStrOne=0;
        var tmpStrTwo=t2.split('|');
        var tmpStrThree;
        var tmpStrFour=0;
        var tmpStrFive=0;
        var tmpStrSix=0;
        tmp_t1=0;
        tmp_t2=0;
        for(tmp_i in tmp_str){
            tmp_str_1=tmp_str[tmp_i][5].split(';');
            for(tmp_j in tmp_str_1){
                tmp_str_2=tmp_str_1[tmp_j].split('|');
                if(tmp_str_2[0]==t1){
                    tmp_str_2[1]=parseInt(tmp_str_2[1]);
                    tmpStrTwo[1]=parseInt(tmpStrTwo[1]);
                    if(tmp_str_2[1]>1 || tmpStrTwo[1]>1){
                        if(tmp_str_2[2].indexOf(',')!=-1){
                            tmpStrThree=tmp_str_2[2].split(',');
                            for(tmp_k in tmpStrThree){
                                tmpStrFour+=parseInt(tmpStrThree[tmp_k]);
                            }
                        }else{
                            tmpStrFour=parseInt(tmp_str_2[2]);
                        }
                        if(tmpStrTwo[1]>1){
                            tmpStrSix=editData.cutScoreToMore(tmpStrFour,tmpStrTwo[1]);
                        }else{
                            tmpStrSix=tmpStrFour;
                        }
                        tmpStrOne=t2+'|'+tmpStrSix;
                        for(tmp_k=3;tmp_k<tmp_str_2.length;tmp_k++){
                            tmpStrOne+='|'+tmp_str_2[tmp_k];
                        }
                    }else{
                        tmpStrOne=tmpStrTwo[0];
                        for(tmp_k=1;tmp_k<tmp_str_2.length;tmp_k++){
                            tmpStrOne+='|'+tmp_str_2[tmp_k];
                        }
                    }
                    //tmpStrOne = this.getTolerantClass(tmpStrOne); //替换时进行库的类别处理
                    tmp_str_1[tmp_j]=tmpStrOne;
                    tmp_str[tmp_i][5]=tmp_str_1.join(';');
                    this.set(tmp_str[tmp_i][0],tmp_str[tmp_i].join('@$@'));
                    return;
                }
            }
        }
    },
    //为多个试题分配分值
    cutScoreToMore:function(score,num){
        if(num<=1) return score;
        var point=0;
        if(score<num) point=1;
        var tmpScore=score/num;
        if(point==1) tmpScore=tmpScore.toFixed(1);
        else tmpScore=Math.floor(tmpScore);
        var output='';
        for(var i=1; i<num; i++){
            output+=','+tmpScore;
        }
        output+=','+(score-tmpScore*(num-1));
        return output.substring(1);
    },
    //answerStyle为0的时候不是联考 saveID为0时候不知道存档是多少
    //attr saveid（存档id） answerstyle（答题卡类型0不是 1统一 2ab ） 学科（学科id,多学科用英文逗号隔开） 综合（0不是 1文综 2理综）
    setAttr:function(){
        this.set('attr','attr@$@0@$@0@$@0@$@0');
        return this.get('attr');
    },
    addAttr:function(saveID,answerStyle){
        var attr=this.get('attr');
        if(!attr){
            attr=this.setAttr();
        }
        attr[1]=saveID;
        attr[2]=answerStyle;
        this.set('attr',attr.join('@$@'));
    },
    getAttr:function(pos){
        var attr=this.get('attr');
        if(!attr){
            attr=this.setAttr();
        }
        return attr[pos];
    },
    editAttr:function(value,pos){
        var attr=this.get('attr');
        if(!attr){
            attr=this.setAttr();
        }
        
        attr[pos]=value;
        this.set('attr',attr.join('@$@'));
    },
    addrectest:function(id,diff,dname,tname){
        var times=new Date().toLocaleString( );//获取日期与时间
        var rectest=this.get('rectest');
        if(!rectest){
            this.set('rectest','rectest'+'@$@'+id+'@%@'+diff+'@%@'+dname+'@%@'+tname+'@%@'+times);
        }else{
            var tmp_str=rectest.join('@$@');
            if(tmp_str.indexOf('@^@'+id+'@%@')==-1 && tmp_str.indexOf('@$@'+id+'@%@')==-1){
                this.set('rectest',tmp_str+'@^@'+id+'@%@'+diff+'@%@'+dname+'@%@'+tname+'@%@'+times);
            }
        }
    },
    delrectest:function(id){
        var rectest=this.get('rectest');
        if(!rectest){
            return;
        }else{
            var tmp_str=rectest[1].split('@^@');
            var tmp_str_1,tmp_j;
            var output=new Array();
            tmp_j=0;
            for(var tmp_i in tmp_str){
                tmp_str_1=tmp_str[tmp_i].split('@%@');
                if(tmp_str_1[0]!=id){
                    output[tmp_j]=tmp_str[tmp_i];
                    tmp_j++;
                }
            }
            if(tmp_j!=0) this.set('rectest',rectest[0]+'@$@'+output.join('@^@'));
            else this.del('rectest');
        }
    },
    //根据记分方式统计该题型下的试题数量
    gettestnum:function(typename,selectType){
        if(typeof(selectType)=='undefined' || selectType==''){
            selectType=0; //0计算小题 1不计算小题
        }
        var tmp_str=this.gettypelist(7);
        var tmp_i;
        var howtotal='';
        var tmp_str_1=new Array;
        var tmp_str_2=new Array;
        var tmp_x;
        var testnum=0;
        var tmp_j;
        for(tmp_i in tmp_str){
            if(tmp_str[tmp_i][2]==typename){
                tmp_str[tmp_i][6]=tmp_str[tmp_i][6].split('|');
                howtotal=tmp_str[tmp_i][6][1];//记分方式 1 小题 2 大题
                if(tmp_str[tmp_i][5]!='0'){
                    tmp_str_1=tmp_str[tmp_i][5].split(';');
                    if(howtotal=='1' && selectType==1){
                        for(tmp_j in tmp_str_1){
                            tmp_str_2=tmp_str_1[tmp_j].split('|');
                            testnum+=parseInt(tmp_str_2[1]);
                        }
                    }else{
                        testnum=tmp_str_1.length;
                    }
                    return testnum;
                    break;
                }
                return 0;
                break;
            }
        }
    },
    //获取选做题数据
    getChooseList:function(testid){
        if(typeof(testid)=='undefined' || testid==''){
            testid=0;
        }
        
        var tmp_str=this.gettypelist(7);
        var tmp_i,tmp_j;
        var tmp_str_1=new Array();
        var tmp_str_2=new Array();
        var test_list=new Array(); //所有选做题数据
        var now_list=''; //当前试题对应选做题题组
        for(tmp_i in tmp_str){
            if(tmp_str[tmp_i][5]!='0'){
                tmp_str_1=tmp_str[tmp_i][5].split(';');
                for(tmp_j in tmp_str_1){
                    tmp_str_2=tmp_str_1[tmp_j].split('|');
                    if(tmp_str_2[3]!='0'){
                        if(testid==tmp_str_2[0]){
                            now_list=tmp_str_1[tmp_j];
                        }
                        if(typeof(test_list[tmp_str_2[3]])=='undefined' || test_list[tmp_str_2[3]]==''){
                            test_list[tmp_str_2[3]]=tmp_str_1[tmp_j];
                        }else{
                            test_list[tmp_str_2[3]]+=';'+tmp_str_1[tmp_j];
                        }
                    }
                }
            }
        }
        return new Array(now_list,test_list);
    },
    //清除所有选做题属性
    chooseClearAll:function(){
        var tmp_str=this.gettypelist(7);
        var tmp_i,tmp_j;
        var tmp_str_1=new Array;
        var tmp_str_2=new Array;
        var now_list=''; //当前试题对应选做题题组
        for(tmp_i in tmp_str){
            if(tmp_str[tmp_i][5]!='0'){
                tmp_str_1=tmp_str[tmp_i][5].split(';');
                for(tmp_j in tmp_str_1){
                    tmp_str_2=tmp_str_1[tmp_j].split('|');
                    if(tmp_str_2[3]!='0'){
                        tmp_str_2[3]=0;
                        tmp_str_2[4]=0;
                        tmp_str_1[tmp_j]=tmp_str_2.join('|');
                    }
                }
                tmp_str[tmp_i][5]=tmp_str_1.join(';');
                this.setvalue(tmp_str[tmp_i][0],tmp_str[tmp_i][5],5);
            }
        }
    },
    //配置选做题testList试题序号以英文逗号间隔 groupNum分组数 doNum选做数量
    chooseSetValue:function(testList,groupNum,doNum){
        var tmp_str=this.gettypelist(7);
        var tmp_i,tmp_j;
        var tmp_str_1=new Array;
        var tmp_str_2=new Array;
        var now_list=''; //当前试题对应选做题题组
        for(tmp_i in tmp_str){
            if(tmp_str[tmp_i][5]!='0'){
                tmp_str_1=tmp_str[tmp_i][5].split(';');
                for(tmp_j in tmp_str_1){
                    tmp_str_2=tmp_str_1[tmp_j].split('|');
                    if((','+testList+',').indexOf(','+tmp_str_2[0]+',')!=-1){
                        tmp_str_2[3]=groupNum;
                        tmp_str_2[4]=doNum;
                        tmp_str_1[tmp_j]=tmp_str_2.join('|');
                    }
                }
                tmp_str[tmp_i][5]=tmp_str_1.join(';');
                this.setvalue(tmp_str[tmp_i][0],tmp_str[tmp_i][5],5);
            }
        }
    },
    //配置选做题testList试题序号以英文逗号间隔 groupNum分组数 doNum选做数量
    getTestIDByOrder:function(orderID){
        if(typeof(orderID)=='undefined' || orderID=='') return 0;
        
        var tmp_str=this.gettypelist(7);
        var tmp_i,tmp_j;
        var tmp_str_1=new Array;
        var tmp_str_2=new Array;
        var thisOrder=0;
        for(tmp_i in tmp_str){
            if(tmp_str[tmp_i][5]!='0'){
                tmp_str_1=tmp_str[tmp_i][5].split(';');
                for(tmp_j in tmp_str_1){
                    tmp_str_2=tmp_str_1[tmp_j].split('|');
                    if(parseInt(tmp_str_2[1])==0) tmp_str_2[1]=1;
                    thisOrder+=parseInt(tmp_str_2[1]);
                    if(thisOrder>=orderID){
                        return tmp_str_2[0];
                    }
                }
            }
        }
        return 0;
    }
}
//试题
jQuery.myTest = {
    //根据题型ID获取该题型的默认分值
    getTypes:function (list,typeid,index){
        var typesval = '';
        var t;
        for(t in list){
            if(list[t]['TypesID'] == typeid){
                typesval = list[t][index];
            }
        }
        return typesval;
    },
    //根据分值和计分方式获取试题分值字符串
    getScoreNum:function(num,score,style){
        num=parseInt(num);
        score=parseInt(score);
        style=parseInt(style);
        var output=1;
        if(style==2 && num>1){
            var tmpScore=score/num;
            var totalScore=0;
            tmpScore=tmpScore.toFixed(1);
            output='';
            for(var ii=0;ii<num-1;ii++){
                output+=','+tmpScore;
                totalScore=totalScore+tmpScore;
            }
            output+=','+(score-totalScore);
            output=output.substr(1);
        }else{
            if(num>1){
                output='';
                for(var ii=0;ii<num;ii++){
                    output+=','+score;
                }
                output=output.substr(1);
            }else{
                output=score;
            }
        }
        return output;
    },
    //更新试题数量$num 试题数  $style试题类型
    updateMainTypes:function(num,style){
        //获取原有题数
        num=parseInt(num || 0);
        var shitinum=0;
        var nowshitinum=0;
        var baifen=0;
        var width=0;
        var f=55;
        var container = $('#quescountdetail tbody', window.parent.document);
        if(container.length == 0){
            container = $('#quescountdetail tbody');
        }
        container.find('tr').each(function(){
            var _this=$(this).find('td:first');
            shitinum+=parseInt(_this.next().next().html().replace('题'));
        });
        shitinum=shitinum+num;
        //设置新题数
        var ifaddhang=1;
        var isadd=0;
        container.find('tr').each(function(){
            var _this=$(this).find('td:first');
            nowshitinum=parseInt(_this.next().next().html().replace('题'));
            if(_this.attr('title')==style && !isadd){
                _this.next().next().html((nowshitinum+num)+'题');
                if(shitinum==0){
                    baifen=0;
                    width=0;
                }else{
                    baifen=(nowshitinum+num)/shitinum*100;
                    width=(nowshitinum+num)/shitinum*f;
                }
                ifaddhang=0;
                isadd=1;
            }else{
                baifen=nowshitinum/shitinum*100;
                width=(nowshitinum)/shitinum*f;
            }
            _this.next().find('.bilibg').css('width',Math.round(width)+'px');
            _this.parent().attr('title','占'+baifen.toFixed(1)+'%');
        });
        if(ifaddhang && num>0){
            //获取分卷
            var crpoint=-1;
            var tmp_j=0;
            var tmp_i;
            var tmp_fj=0;
            if(typeof(addTypes)=='undefined' || addTypes==''){
                addTypes = parent.Types;
            }
            if(typeof(subjectID)=='undefined' || subjectID==''){
                subjectID=Cookie.Get("SubjectId");
            }
            for(tmp_i in addTypes[subjectID]){
                if(style==addTypes[subjectID][tmp_i]['TypesName']){
                    tmp_fj=addTypes[subjectID][tmp_i]['Volume'];
                    break;
                }
            }
            var trlen=container.find('tr').length;
            //获取分卷1的结尾
            var tmp_str_1,tmp_str_2;
            var tmp_str=editData.getall();
            tmp_str_1=tmp_str.split('@#@');
            for(tmp_i in tmp_str_1){
                tmp_str_2=tmp_str_1[tmp_i].split('@$@');
                if(tmp_str_2[0].indexOf('questypehead'+tmp_fj+'_')!=-1){
                    crpoint=crpoint+1;
                }
            }
            if(trlen==0){
                crpoint=-1;
            }else if(tmp_fj==2){
                crpoint=trlen-1;
            }else if(tmp_fj==1 && crpoint==-1){
                crpoint=-1;
            }else{
                crpoint=crpoint-1;
            }

            baifen=num/shitinum*100;
            width=num/shitinum*f;
            var tmp_str_n='<tr title="占'+baifen.toFixed(1)+'%">'+
                '<td align="right" title="'+style+'"'+ (style.length>6 ? " width='105' " : '') +'>'+style+'：</td>'+
                '<td align="left">'+
                '<span class="bilibox" style="width:55px;">'+
                '<span class="bilibg" style="width:'+width+'px;"/>'+
                '</span>'+
                '</td>'+
                '<td align="right">'+num+'题</td>'+
                '<td>'+
                '<a class="emptyquestype" href="javascript:void(0);" title="清空 '+style+'"/>'+
                '</td>'+
                '</tr>';
            if(crpoint!=-1) container.find('tr').eq(crpoint).after(tmp_str_n);
            else container.html(tmp_str_n+container.html());
        }
        var count = $('#quescount',window.parent.document);
        if(count.length == 0){
            count = $('#quescount');
        }
        count.html(shitinum);
    },
    //添加题型到试题篮
    addMainTypes:function(name,num,ii){
        var container = $('#quescountdetail tbody', window.parent.document);
        if(container.length == 0){
            container = $('#quescountdetail tbody');
        }
        container.find('tr').each(function(i){
            if((i+1)==ii){
                $(this).after('<tr title="占0.0%">'+
                    '<td align="right" title="'+name+'"'+(name.length>6 ? " width='105' " : '')+'>'+name+'：</td>'+
                    '<td align="left">'+
                    '<span class="bilibox" style="width:55px;">'+
                    '<span class="bilibg" style="width:0px;"/>'+
                    '</span>'+
                    '</td>'+
                    '<td align="right">'+num+'题</td>'+
                    '<td>'+
                    '<a class="emptyquestype" href="javascript:void(0);" title="清空 '+name+'"/>'+
                    '</td>'+
                    '</tr>');
                return;
            }
        });
        if(num>0) updateMainTypes(num,name);
    },
    //显示渐变色 试卷中心使用特效
    showAlpha:function (tmp_id){
        if(tmp_id.find('.effect').length==0){
            tmp_id.append('<div class="effect"></div>');
            tmp_id.css({'position':'relative'});
            tmp_id.find('.effect').css({"top":"0px","left":"0px","position": "absolute", "z-index": 9999999, "background-color": "rgb(255, 255, 0)"});
            tmp_id.find('.effect').width(tmp_id.outerWidth(true));
            tmp_id.find('.effect').height(tmp_id.outerHeight(true));
        }
        tmp_id.find('.effect').css({'display':'block','opacity':0.6});
        tmp_id.find('.effect').animate({opacity:0},500,function(){
            tmp_id.find('.effect').css({'display':'none'});
        });
    },
    //显示星星不可编辑
    starShow:function(score){
        var maxScore=10; //最大分值
        var starNum=5; //星星数
        if(typeof(score)=='undefined' || score<0 || score>maxScore) score=maxScore/2;
        var starLimit=score*starNum/maxScore;
        var starH=Math.floor(starLimit);
        var starJ=starLimit==starH ? 0 : 1;
        var starG=starNum-starH-starJ;
        var output='<span class="icon-star star-unable">';
        for(var i=0;i<starH;i++){
            output+='<a class="staryellow"></a>';
        }
        for(var i=0;i<starJ;i++){
            output+='<a class="starPoint"></a>';
        }
        for(var i=0;i<starG;i++){
            output+='<a class="stargray"></a>';
        }
        output+='</span><span class="star-score">'+score+'分'+'</span>';
        return output;
    },
    //显示星星可编辑
    starSpan:function(score){
        var maxScore=10; //最大分值
        var starNum=5; //星星数
        if(typeof(score)=='undefined' || score<0 || score>maxScore) score=maxScore/2;
        var starLimit=score*starNum/maxScore;
        var starH=Math.floor(starLimit);
        var starJ=starLimit==starH ? 0 : 1;
        var starG=starNum-starH-starJ;
        var output='<span class="scorebox icon-star">';
        for(var i=0;i<starH;i++){
            output+='<a class="start start1"></a>';
        }
        for(var i=0;i<starJ;i++){
            output+='<a class="start start2"></a>';
        }
        for(var i=0;i<starG;i++){
            output+='<a class="start start3"></a>';
        }
        output+='</span><span class="star-score">'+
            '<span class="quesscore" val="'+score+'">'+score+'</span>分'+
            '</span>';
        return output;
    },
    starSetImg:function(scoreBox,score){
        var index = Math.floor(parseInt(score)/2);
        var part = parseInt(score) % 2;
        for (var i = 0, len = scoreBox.find("a.start").length; i < len; i++) {
            if (i < index) {
                scoreBox.find("a.start").eq(i).removeClass().addClass("start start1");
            } else {
                scoreBox.find("a.start").eq(i).removeClass().addClass("start start3");
            }
        }
        if (part == 1) {
            scoreBox.find("a.start").eq(index).removeClass().addClass("start start2");
        }
    },
    starEvent:function(){
        //评论标星
        $("a.start").live('click',function() {
            var quesScore=$(this).parents(".scorebox").last().next().find('.quesscore');
            quesScore.attr("val", quesScore.html());
        });
        $("a.start").live('mouseout',function() {
            var scoreBox=$(this).parents(".scorebox").last();
            var quesScore=scoreBox.next().find('.quesscore');
            var score = quesScore.attr("val");
            quesScore.html(score);
            $.myTest.starSetImg(scoreBox,score);
        });
        $("a.start").live('mousemove',function(e) {
            var _this=$(this);
            var scoreBox=_this.parents(".scorebox").last();
            var quesScore=scoreBox.next().find('.quesscore');

            var boxLeft = scoreBox.position().left;
            var mouseLeft = e.clientX;
            var width = scoreBox.find("a.start").eq(0).width();
            var left=0;
            if(_this.parents('.dialog').length>0){
                left=_this.parents('.dialog').last().position().left;
            }
            var val = (mouseLeft - boxLeft - left) / width;

            var len = scoreBox.find("a.start").length;

            var score = 0;
            if (val >= len) {
                scoreBox.find("a.start").removeClass().addClass("start start1");
                score = len;
            } else {
                val = parseFloat("0." + val.toString().split(".")[1]);
                var cssname = "start";
                var index = scoreBox.find("a.start").index(_this);
                if (val > 0.75) { cssname = "start start1"; score = index + 1; }
                else if (val < 0.25) { cssname = "start start3"; score = index; }
                else { cssname = "start start2"; score = index + 0.5; }
                for (var i = 0; i < len; i++) {
                    if (i < index) { scoreBox.find("a.start").eq(i).removeClass().addClass("start start1"); }
                    else { scoreBox.find("a.start").eq(i).removeClass().addClass("start start3"); }
                }
                $(this).removeClass().addClass(cssname);
            }
            score = parseFloat(score).toFixed(1);
            score=(score == 0) ? "0" : score*2;
            quesScore.html(score);
        });
    },
    //试题反馈事件绑定
    commentTestEvent:function(){
        //绑定试题反馈窗口
        $('a.comment').live('click',function(){
            var idname='commentdiv';
            var tmp_id=$(this).attr('id').replace('comment','');
            $.myDialog.tcLoadDiv('试题反馈 [题号='+tmp_id+']',550,idname);
            var tmp_str='<div id="commentdata" style="display:none;" quesid="'+tmp_id+'"></div>'+
                '<div class="commentAreaBox">'+
                '<textarea cols="1" rows="1" id="comment">我来说两句~</textarea>'+
                '<div class="commentStar">'+
                '<table border="0" cellpadding="0" cellspacing="0" width="100%">'+
                '<tbody>'+
                '<tr>'+
                '<td width="1"></td>'+
                '<td>'+
                '<table border="0" cellpadding="0" cellspacing="0">'+
                '<tbody>'+
                '<tr>'+
                '<td style="padding-left:10px;font-weight:bold;">顺便打个分：</td>'+
                '<td>'+ $.myTest.starSpan()+
                '</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'+
                '</td>'+
                '<td height="40">'+
                '<div class="an01 bgbt" id="commentSubmit" did="'+tmp_id+'" style="float:right; margin-top:5px; margin-right:10px"><span class="an_left"></span><a>提交</a><span class="an_right"></span></div>'+
                '</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'+
                '</div>'+
                '</div>'+
                '<div>'+
                '<div id="commentbox"><div style="width:540px; height:240px; margin:10px auto 0px auto; overflow: auto; overflow-x:hidden"></div></div>'+
                '<div id="summary"></div>'+
                '</div>';
            $('#'+idname+' .content').html(tmp_str);
            $.myDialog.tcShowDiv(idname);
            $('#div_shadow'+idname).css({'display':'block'});
            $.myTest.getCommentList(tmp_id);

            $('#comment').live('click',function(){
                if($(this).val()=='我来说两句~'){
                    $(this).val('');
                }
            });
        });
        //提交试题反馈
        $('#commentSubmit').live('click',function(){
            var quesid = $('#commentdata').attr('quesid');
            var comment = $.trim($("#comment").val());
            var quesscore = $('#commentdiv .quesscore').attr("val");

            if (comment.length == 0 || comment=='我来说两句~') {
                $.myDialog.showMsg("试题反馈没有填写。",1);
                $("#comment").val('');
                $("#comment").focus();
                return;
            }
            var data = {
                'comment': comment,
                'quesid': quesid,
                'quesscore': quesscore,
                'times':Math.random()
            };
            $.ajax({
                url: U('Home/Index/comment'),
                type: "post",
                data: data,
                success: function(data) {
                    if($.myCommon.backLogin(data)==false){
                        return false;
                    };
                    if (data['data'].indexOf("true") == 0) {
                        $('#commentdiv .tcClose').click();
                        $.myDialog.showMsg('评论成功！');
                    }else{
                        $.myDialog.showMsg(data['data'],1);
                    }
                },
                error: function() { $.myDialog.showMsg('评论失败！',1); }
            });
        });
        //评论上一页
        $('.comprevpage').live('click',function(){
            var page=parseInt($('.comcurpage').html());
            $.myTest.getCommentList($('#commentSubmit').attr('did'),page-1);
        });
        //评论下一页
        $('.comnextpage').live('click',function(){
            var page=parseInt($('.comcurpage').html());
            $.myTest.getCommentList($('#commentSubmit').attr('did'),page+1);
        });
        //载入星星事件
        $.myTest.starEvent();
    },

    //试题评论列表
    getCommentList:function(tmp_id,page){
        if(typeof(page)=='undefined' || page<1) page=1;
        //评论
        $.post(U('Home/Index/commentList'),{id:tmp_id,curpage:page,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            var totalpage=Math.ceil(data['data'][1]/data['data'][2]);
            if(page>totalpage) page=totalpage;

            var tmp_str='';
            for(var tmp_i in data['data'][0]){
                tmp_str+='<div style="padding:15px 0px;margin:0px 8px; border-bottom:#ccc dashed 1px">'+
                    '<div style="font-size:14px; color:#0074d9; padding-bottom:5px">'+data['data'][0][tmp_i]['UserName']+'&nbsp;&nbsp;<font style="font-size:12px; font-family:Arial; color:#999">'+new Date(data['data'][0][tmp_i]['LoadDate']*1000).toLocaleString()+'</font></div>'+
                    '<div style="font-size:14px; color:#555; line-height:24px">'+data['data'][0][tmp_i]['Content']+'</div></div>';
            }
            $('#commentbox div').html(tmp_str);
            tmp_str='<div style="border-top:1px dotted #1C6397;">'+
                '<table width="100%" style="color:#666"><tbody>'+
                '<tr><td>'+
                '总<b>'+data['data'][1]+'</b>个评价'+
                '</td>'+
                '<td align="right">第<span class="comcurpage">'+page+'</span>页/共<span class="pagecount">'+totalpage+'</span>页。'+
                '<a class="comprevpage" href="javascript:void(0);">上一页</a>'+
                '<a class="comnextpage" href="javascript:void(0);">下一页</a>'+
                '</td></tr>'+
                '</tbody></table>'+
                '</div>';
            $('#summary').html(tmp_str);
        });
    },
    //纠错试题事件绑定
    correctTestEvent:function(){
        window.UEDITOR_HOME_URL = '/Public/plugin/ueditor/';//根路径定义
        //加载文件
        var urls = [];
        urls.push("/Public/plugin/ueditor/ueditor.config.js","/Public/plugin/ueditor/ueditor.all.js","/Public/plugin/editor.js");
        load_script(urls);

        $('a.correction').live('click',function(){
            var ueObj = '';
            var x;
            var errortype=[['1','试题内容'],['2','答案解析'],['3','所属章节'],['4','知识点属性'],['0','其他']];
            var idname='correctiondiv';
            $.myDialog.normalMsgBox(idname,'加载中',620, $.myCommon.loading());//提示语
            var tmp_id=$(this).attr('id').replace('correction','');
            var title='试题纠错 [题号='+tmp_id+']';
            var tmp_str='<div id="correctiondata" style="display:none;" quesid="'+tmp_id+'"></div>'+'<div style="font-size:14px;"><p>题目编号：'+tmp_id+'</p><p><span>错误类型：</span><span>';
            for(x in errortype){
                tmp_str+='<label><input type="checkbox" value="'+errortype[x][0]+'" name="errortype" class="errortype" title="'+errortype[x][1]+'">'+errortype[x][1]+'&nbsp;&nbsp;</label>';
            }
            tmp_str+='</span></p></div>'+
                '<p>错误描述：</p><div class="correctionBox"><div style="height:215px;" name="correction_val" class="correctionVal"></div>'+'</div>';
            $.myDialog.normalMsgBox(idname,title,620,tmp_str,3);
            ueObj = $.Editor.setEditor(
                 U('Manual/Index/upload?dir=correctTest'),
                 '.correctionVal',
                 '我来说两句~',
                 {
                     toolbars: [['bold', 'italic', 'underline', '|', 'fontsize', 'forecolor', '|', 'simpleupload', 'scrawl', 'wordimage']],initialFrameHeight:180,'textarea' : 'Content','autotypeset':'','allowDivTransToP':false,'initialFrameWidth' : '100%'}
            );

            $('#correctiondiv .normal_yes').on('click',function(){
                var content=ueObj.getContent();
                $('#correction_val').val('');

                var tmp_id=$('#correctiondata').attr('quesid');
                var title='试题纠错 [题号='+tmp_id+']';
                var subjectID=Cookie.Get('SubjectId');
                var typeid='';
                $("input[name='errortype']:checked").each(function(){
                    typeid+=$(this).val()+',';
                })
                if(typeid==''){
                    typeid=0;
                }
                if(content=='' || content=='我来说两句~'){
                    $.myDialog.showMsg('错误描述不能为空!',1);
                    return false;
                }
                if(content.length>350){
                    $.myDialog.showMsg('您回复的文字太长了！',1);
                    return false;
                }
                $.ajax({
                    url: U('Home/Index/correct'),
                    type: "post",
                    data: {testID:tmp_id,correctcontent:content,TypeID:typeid,SubjectId:subjectID,'times':Math.random()},
                    success: function(data) {
                        if($.myCommon.backLogin(data)==false){
                            return false;
                        };
                        if (data['status'] == '1') {
                            $('#correctiondiv .tcClose').click();
                            $.myDialog.showMsg('纠错信息提交成功！');
                        }else{
                            $.myDialog.showMsg(data['data']['msg'],1);
                            var errortype=[['1','试题内容'],['2','答案解析'],['3','所属章节'],['4','知识点属性'],['0','其他']];
                            var tmp_str='<div id="correctiondata" style="display:none;" quesid="'+tmp_id+'"></div><div style="font-size:12px;"><p>题目编号：'+tmp_id+'</p><p>错误类型：';
                            for(x in errortype){
                                tmp_str+='<label><input type="checkbox" value="'+errortype[x][0]+'" name="errortype" class="errortype" title="'+errortype[x][1]+'">'+errortype[x][1]+'&nbsp;&nbsp;</label>';
                            }
                            tmp_str+='</p></div><p>错误描述：</p>'+'<div style="border:1px solid #e0e0e0;padding:0px;margin:3px 5px;">'+'<textarea cols="1" rows="1" id="correction_val">我来说两句~</textarea>'+
                                '</div>';
                            $.myDialog.normalMsgBox('correctiondiv',title,500,tmp_str,3);
                        }
                    },
                    error: function() { $.myDialog.showMsg('纠错信息提交失败！',1); }
                });
            });
        });
    },
    //收藏试题事件绑定
    favTestEvent:function(){
        $('.fav').live('click',function(){
            var tmp_id=$(this).attr('id').replace('fav','');
            var tmp_name=$(this).attr('thisquestitle');
            var str='';

            $.post(U('User/Home/getCanUseCata'),{'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                str='';
                if(data['data']){
                    str +='<a class="bank_current" cid="0" title="收藏到该目录">未分类</a>';
                    for(var i in data['data']){
                        if(data['data'][i]['deep']){
                            for(var j=0;j<data['data'][i]['deep'].length;j++){
                                str +='<a class="bank" cid="'+data['data'][i]['deep'][j]['CatalogID']+'" title="收藏到该目录">'+data['data'][i]['deep'][j]['CatalogName']+'</a>';
                            }
                        }else{
                            str +='<a class="bank" cid="'+data['data'][i]['CatalogID']+'" title="收藏到该目录">'+data['data'][i]['CatalogName']+'</a>';
                        }
                    }
                }else{
                    str+='<a class="bank_current" cid="0" title="收藏到该目录">未分类</a>';
                }
                var cata_str='<div id="catafav"><input type="hidden" id="tmp_id" value="'+tmp_id+'" /><input type="hidden" id="tmp_name" value="'+tmp_name+'"><div class="catafavinfo">'+str+'</div></div>';
                $.myDialog.normalMsgBox('catafavdiv','收藏至',450,cata_str,3);
            })
        });
        //试题收藏选择收藏文件夹事件
        $('#catafav a').live('click',function(){
            $('#catafav a').attr('class','bank');
            $(this).attr('class','bank_current');
        });
        //试题收藏确定
        $('#catafavdiv .normal_yes').live('click',function(){
            var tmp_id=$('#tmp_id').val();
            var tmp_name=$('#tmp_name').val();
            var tmp_cataid=$('#catafav .bank_current').attr('cid');
            $.myDialog.showMsg('加入收藏夹请稍候...',0,0);
            $.ajax({
                type: "post",
                url: U("Home/Index/favSave"),
                data: { id: tmp_id,favname: tmp_name,catalogid:tmp_cataid,'times':Math.random()},
                success: function(data) {
                    if($.myCommon.backLogin(data)==false){
                        return false;
                    };
                    $('#catafavdiv .tcClose').click();
                    if (data['data'].toLowerCase() == "true") {
                        $.myDialog.showMsg('收藏成功！');
                    } else {
                        $.myDialog.showMsg(data['data'],1);
                    }
                },
                error: function() { $('#favdiv .tcClose').click(); $.myDialog.showMsg('收藏失败',1);}
            });
        });
    },

    //显示试题
    showTest:function(arr){
        var output='';
        for(var i=0;i<arr.length;i++){
            if(arr[i]['testid']==null || arr[i]['testid']=='' ||  arr[i]['testid']=='undefined') continue;
            var sourcePic='';
            if(arr[i]['sourceid']!='0' && arr[i]['sourceid']=='undefined' && arr[i]['sourceid']!=''){
                sourcePic='， 来源:</td><td><img src="'+arr[i]['sourcePic']+'"/>';
            }
            output+='<div class="quesbox" id="quesbox'+arr[i]['testid']+'">';
            output+='<div class="quesbox_inner">'+
                '<div class="quesinfobox">'+
                '<div class="quesinfo_tit">标题/来源：<span class="questitle">'+arr[i]['docname']+'</span></div>'+
                '<div><table border="0" cellpadding="0" cellspacing="0">'+
                '<tbody><tr>'+
                '<td>题号：'+arr[i]['testid']+'，题型：'+arr[i]['typesname']+'，难度：</td>'+
                '<td>'+arr[i]['diffxing']+sourcePic+'</td>'+
                '</tr></tbody>'+
                '</table></div>';
            output+='<div class="quesmenu">&nbsp;&nbsp;日期：'+arr[i]['firstloadtime']+'&nbsp;&nbsp;</div>';
            output+='</div>';

            output+='<div class="quesdiv" id="quesdiv'+arr[i]['testid']+'" onselectstart="return false;" oncopy="return false;" style="-moz-user-select:none;">'+
                '<div class="quesbody">'+arr[i]['test']+'</div>'+
                '<div class="quesanswer" tid="'+arr[i]['testid']+'" show="0"><p class="list_ts"><span class="ico_dd">载入数据请稍候...</span></p></div>'+
                '</div>';
            output+='<div class="quesinfobox">';
            output+='<div class="quesother"><a id="fav'+arr[i]['testid']+'" class="fav" title="收藏试题" thisquestitle="'+arr[i]['docname']+'"/><a id="comment'+arr[i]['testid']+'" class="comment" title="评价试题"/><a id="correction'+arr[i]['testid']+'" class="correction" title="纠错试题"/></div>';
            output+='<div class="quesmenu">';
            var sty="";
            var classname='addquessel';
            if(editData.ifhavetest(arr[i]['testid'])){
                classname='delques';
                sty='style="display:none;"';
            }

            //判断是否加上特殊属性
            var insertStr='';
            if(typeof(arr[i]['score'])!='undefined'){
                insertStr+=' score="'+arr[i]['score']+'" ';
            }
            if(typeof(arr[i]['chooseType'])!='undefined'){
                insertStr+=' chooseType="'+arr[i]['chooseType']+'" ';
            }
            if(typeof(arr[i]['chooseNum'])!='undefined'){
                insertStr+=' chooseNum="'+arr[i]['chooseNum']+'" ';
            }

            output+='<span class="singleDown bgbt an01"><span class="an_left"></span><a href="'+U('Manual/Index/singleDown?id='+arr[i]['testid'])+'" target="_blank">单题下载</a><span class="an_right"></span></span>' +
                '<a id="quesselect'+arr[i]['testid']+'" class="'+classname+'" quesid="'+arr[i]['testid']+'" childnum="'+arr[i]['testnum']+'" questitle="'+arr[i]['docname']+'" qyid="'+arr[i]['typesid']+'" qyname="'+arr[i]['typesname']+'" '+insertStr+' qdid="'+arr[i]['diffid']+'" qdname="'+arr[i]['diffname']+'"/></a><span class="selmore" childnum="'+arr[i]['testnum']+'" qyid="'+arr[i]['typesid']+'" id="selmore'+arr[i]['testid']+'" '+sty+' testid='+arr[i]['testid']+'></span><span class="selpicleft" id="selpicleft'+arr[i]['testid']+'"'+sty+'></span>';
            output+='</div>'+
                '</div>'+
                '<div class="quesparse"></div>';
            output+='</div>';
            output+='</div>';
        }
        return output;
    },
    //加入试题
    addTest:function(){
        $('.addquessel').live('click',function(){
            var typeid=$(this).attr('qyid');
            var typename=$(this).attr('qyname');
            var result= $.myTest.checkIfOver(typeid,typename);
            if(!result[0]){
                $.myDialog.showMsg('题型【'+result[1]+'】的题数已超出限制数量！',1);
                return false;
            }else{
                if(!editData.addtest($(this).attr('quesid'),$(this).attr('childnum'),$(this).attr('qyname'),$(this).attr('qyid'),$(this).attr('score'),$(this).attr('choosenum'),$(this).attr('choosetype'))){
                    return false;
                }

                $.myTest.updateMainTypes($(this).attr('childnum'),$(this).attr('qyname'));
                var testid=$(this).attr('quesid');
                $('#selmore'+testid).hide();
                $('#selpicleft'+testid).hide();
                $(this).addClass('delques');
                $(this).removeClass('addquessel');
            }
        });
    },
    //试题替换，查找类型相同的试题
    getSameTest:function(tmp_id,tmp_allid,idName){
        $.post(U('Home/Index/getSameById'),{"id":tmp_id,"allid":tmp_allid,"rand":Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $.myDialog.tcCloseDiv(idName);//失败时关闭数据加载提示信息
                return false;
            }
            var total=data['data']['total'];
            var dataArr=data['data']['data'];
            var tmp_str='<div id="diaquesswitch" class="rel pagebox">';
            var tmp_len=dataArr[1];
            if(tmp_len>10) tmp_len=10;
            for(var tmp_i=0;tmp_i<tmp_len;tmp_i++){
                tmp_str+='<a class="diaques">'+(tmp_i+1)+'</a>';
            }
            if(total>10){
                tmp_str+='<a class="sameRefresh" href="javascript:;" idname="'+idName+'" tmpId="'+tmp_id+'" tmpAllId="'+tmp_allid+'">刷新</a>';
            }
            tmp_str+='<a class="replaceTest diarepalcebtn" href="#">确认替换</a>';
            tmp_str+='</div>';

            tmp_str+='<div id="diaqueslistbox">';
            for(tmp_i in dataArr[0]){
                tmp_str+='<div class="diaquesbox" style="position: relative; cursor: default; display: none;">'+
                '<div class="diaquestitle">'+
                '<table border="0"><tbody><tr>'+
                '<td>题号ID：<span style="font-weight:bold;">'+dataArr[0][tmp_i]['testid']+'</span>'+
                '，难度：<span>'+dataArr[0][tmp_i]['diffname']+'</span>'+
                '，标题：<span>'+dataArr[0][tmp_i]['docname']+'</span>'+
                '</td>'+
                '<td align="right">'+
                '</td>'+
                '</tr></tbody></table>'+
                '</div>'+
                '<div class="diaquescontent" style="padding:5px;line-height:18px;">'+
                '<div class="diaquesbody">'+
                '<div>'+ $.myTest.changeTagToNum(dataArr[0][tmp_i]['test'],1)+'</div>'+
                '</div>'+
                '<div class="diaquesanswerparse">'+
                '<div><p><font color="#00a0e9">【答案】</font>'+$.myTest.changeTagToNum(dataArr[0][tmp_i]['answer'],1)+'</div>'+
                '<div><p><font color="#00a0e9">【解析】</font>'+$.myTest.changeTagToNum(dataArr[0][tmp_i]['analytic'],1)+'</div>'+
                '<div><p><font color="#00a0e9">【备注】</font>'+$.myTest.changeTagToNum(dataArr[0][tmp_i]['remark'],1)+'</div>'+
                '</div>'+
                '</div>'+
                '</div>';
            }
            tmp_str+='</div>';
            $('#'+idName+' .content').html(tmp_str);
            $.myDialog.tcDivPosition(idName);
            $.myTest.showDiaTest(0);//替换  试题的显示层
        });
    },
    //替换  试题的显示层
    showDiaTest:function(num){
        if($('#diaquesswitch').length>0){
            $('#diaquesswitch a').removeClass('divques_current');
            $('#diaquesswitch a:eq('+num+')').addClass('divques_current');
            $('#diaqueslistbox .diaquesbox').css({'display':'none'});
            $('#diaqueslistbox .diaquesbox:eq('+num+')').css({'display':'block'});
        }
    },
    //试题下拉，弹出框，选入试题入栏  2014/8/20
    addTestBySelect:function(){
        $('.selmore').live('click',function(){
            var typemsg=editData.gettypename();
            var testid=$(this).attr('testid');
            var num=$(this).attr('childnum');
            var qyid=$(this).attr('qyid');
            var len=typemsg.length;
            var testid=$(this).attr('testid');
            var title='选入试题【编号：'+testid+'】入栏';
            var idname='testinsert';
            var i;
            var tmp_str='';
            tmp_str+='请选择所加入：<select name="testtypemsg" id="testtypemsg">'+
                '<option value="" text="reempty">请选择</option>';
            for(i=0;i<len;i++){
                tmp_str+='<option value="'+typemsg[i][0]+'">'+typemsg[i][1]+'</option>';
            }
            tmp_str+='</select>';
            tmp_str+='<input type="hidden" name="testid" id="thistestid" value="'+testid+'">';
            tmp_str+='<input type="hidden" name="num" id="num" value="'+num+'">';
            tmp_str+='<input type="hidden" name="qyid" id="qyid" value="'+qyid+'">';
            $.myDialog.normalMsgBox(idname,title,350,tmp_str,3);
        });
        $('#testinsert .normal_yes').live('click',function(){
            if($(this).attr('only')=='0'){
                return false;
            }
            var typedivid=$('#testtypemsg option:selected').val();
            var typename=$('#testtypemsg option:selected').text();
            var thistestid=$('#thistestid').val();
            var qyid=$('#qyid').val();
            var num=$('#num').val();
            if(typedivid==''){
                $.myDialog.showMsg('您还没有选择题型!',1);
                return false;
            }
            $(this).attr('only','0');
            //试题ID 小题数 题型名称 题型ID
            //检验添加的试题是否超出
            var result= $.myTest.checkIfOver(qyid,typename);
            if(!editData.addtest(thistestid,num,typename,qyid))
                return false;
            $.myTest.updateMainTypes(num,typename);
            $('#selmore'+thistestid).hide();
            $('#selpicleft'+thistestid).hide();
            $('#quesselect'+thistestid).addClass('delques');
            $('#quesselect'+thistestid).removeClass('addquessel');
            $('#testinsert .tcClose').click();
        });
    },
    //移除试题
    removeTest:function(){
        $('.delques').live('click',function(){
            var tmp_str=editData.selecttest($(this).attr('quesid'));
            var testid=$(this).attr('quesid');
            $('#selmore'+testid).css('display','inline-block');//样式不能show
            $('#selpicleft'+testid).css('display','inline-block');//样式不能show
            if(tmp_str){
                editData.deltest($(this).attr('quesid'));
                $.myTest.updateMainTypes(0-$(this).attr('childnum'),tmp_str);
                $(this).addClass('addquessel');
                $(this).removeClass('delques');
            }
        });
    },
    //显示或隐藏答案 点击试题题文
    showTestMoreByBody:function(){
        $('.quesbody').live('click',function(){
            var adiv=$(this).next('.quesanswer');
            var tid=adiv.attr('tid');
            if(tid=='0') return;
            if($(this).next('.quesanswer').css('display')=='block'){
                $(this).next('.quesanswer').css('display','none');
                $(this).parent().find('.quesparse').css('display','none');
                $(this).parent().find('.quesremark').css('display','none');
            }else{
                if($(this).next('.quesanswer').attr('show')==0){
                    $.post(U('Home/Index/getOneTestById'),{'id':tid,'width':500,'s':Math.random()},function(data){
                        if($.myCommon.backLogin(data)==false){
                            return false;
                        };
                        if(data['data'][0]=='success'){
                            var str='<div class="quesanswer_tit">答案</div>'+data['data'][1][0][0]['answer'];
                            if(data['data'][1][0][0]['analytic']!='' && data['data'][1][0][0]['analytic']!='</p>'){
                                str+='<div class="quesanswer_tit">解析</div>'+data['data'][1][0][0]['analytic'];
                            }
                            if(data['data'][1][0][0]['kllist']){
                                str+='<div class="quesanswer_tit">知识点</div><div>'+data['data'][1][0][0]['kllist']+'</div>';
                            }
                            if(data['data'][1][0][0]['remark'] && data['data'][1][0][0]['remark']!='</p>'){
                                str+='<div class="quesanswer_tit">备注</div>'+data['data'][1][0][0]['remark'];
                            }
                            adiv.html(str);
                            adiv.attr('show',1);
                        }else{
                            alert(data['data']);
                        }
                    });
                }
                $(this).next('.quesanswer').css('display','block');
                $(this).parent().find('.quesparse').css('display','block');
                $(this).parent().find('.quesremark').css('display','block');
            }
        });
    },
    //显示或隐藏答案
    showTestMoreByAnswer:function(){
        $('.quesanswer').live('click',function(){
            if($(this).css('display')=='block'){
                $(this).css('display','none');
                $(this).parent().find('.quesparse').css('display','none');
                $(this).parent().find('.quesremark').css('display','none');
            }else{
                $(this).css('display','block');
                $(this).parent().find('.quesparse').css('display','block');
                $(this).parent().find('.quesremark').css('display','block');
            }
        });
        $('.quesparse').live('click',function(){
            if($(this).css('display')=='block'){
                $(this).css('display','none');
                $(this).parent().find('.quesanswer').css('display','none');
                $(this).parent().find('.quesremark').css('display','none');
            }else{
                $(this).css('display','block');
                $(this).parent().find('.quesanswer').css('display','block');
                $(this).parent().find('.quesremark').css('display','block');
            }
        });
        $('.quesremark').live('click',function(){
            if($(this).css('display')=='block'){
                $(this).css('display','none');
                $(this).parent().find('.quesanswer').css('display','none');
                $(this).parent().find('.quesparse').css('display','none');
            }else{
                $(this).css('display','block');
                $(this).parent().find('.quesanswer').css('display','block');
                $(this).parent().find('.quesparse').css('display','block');
            }
        });
    },

    //载入试题事件
    showTestEvevt:function(){
        this.addTest(); //添加试题
        this.addTestBySelect(); //选择添加试题到题型
        this.removeTest(); //移除试题
        this.showTestMoreByBody(); //关闭或显示试题详细信息
        this.showTestMoreByAnswer(); //关闭或显示试题详细信息
        this.favTestEvent(); //收藏试题事件绑定
        this.correctTestEvent(); //纠错试题事件绑定
        this.commentTestEvent(); //试题反馈事件绑定

        $.myCommon.loadVideoFrame(); //载入视频事件
    },

    //验证试题数量是否超出
    checkIfOver:function(typeid,typename){
        var subjectID=Cookie.Get('SubjectId');
        var result=new Array();
        var typearr=parent.Types[subjectID];
        var i;
        for(i=0;i<typearr.length;i++){
            if(typearr[i]['TypesID']==typeid){
                var num=typearr[i]['Num'];
                var typename=typearr[i]['TypesName'];
                var selectType=typearr[i]['SelectType']; //1计算小题 0不计算小题
            }
        }
        var nownum=editData.gettestnum(typename,selectType);

        if((parseInt(nownum)+1)>parseInt(num)){
            result[0]=false;
            result[1]=typename;
        }else{
            result[0]=true;
            result[1]=typename;
        }
        return result;
    },
    //处理试题标签变成序号
    changeTagToNum:function(str,startNum,hanzi){
        var tmpFlag=0;
        if(str.indexOf('【小题')!=-1){
            str=str.replace(/【小题[0-9]*】/g,'<span class="quesindex"><b></b></span><span class="quesscore"></span>');
            str=$.myTest.addNumToCutStr(str,'<span class="quesindex"><b>',startNum,'．');
            tmpFlag=1;
        }

        if(str.indexOf('【题号')!=-1){
            str=str.replace(/【题号[0-9]*】/g,'<span class="quesindexnum">　　</span>');
            str=$.myTest.addNumToCutStr(str,'<span class="quesindexnum">　',startNum);
            tmpFlag=1;
        }
        if(typeof(hanzi)=='undefined'){
            hanzi=0;
        }
        if(tmpFlag==0 && hanzi==0){
            str='<p><span class="quesindex"><b>'+startNum+'．</b></span><span class="quesscore"></span><span class="tips"/>'+ $.myTest.removeLeftTag(str,'<p>');
        }
        if(tmpFlag==0 && hanzi==1){
            str='<p><span class="quesindex"><b>'+shuzi[startNum-1]+'．</b></span><span class="quesscore"></span><span class="tips"/>'+ $.myTest.removeLeftTag(str,'<p>');
        }
        return str;
    },
    //在字符串中指定字符串添加序号
    addNumToCutStr:function(str,cutStr,startNum,separator){
        if(typeof(separator)=='undefined') separator='';
        var tmpArr=str.split(cutStr);
        if(tmpArr.length<2) return str;

        for(var i in tmpArr){
            if(i==0) continue;
            tmpArr[i]=startNum+separator+tmpArr[i];
            startNum++;
        }
        return tmpArr.join(cutStr);
    },
    //去除开头p
    removeLeftTag:function(str,tag){
        if(str.indexOf(tag)==0){
            return str.substring(tag.length);
        }
        return str;
    },
    //处理校本题库标签前台显示问题
    //@notice 根据前台显示结果 或需再调整
    manCustomTestTag:function(str){
        if(str=='') return '';
        str=this.removeLeftTag(str,'<p>');
        str=this.removeLeftTag(str,'<p mode="mark"></p>');
        str=this.removeLeftTag(str,'<p mode="mark">');
        str=this.removeLeftTag(str,'<p>');
        return str;
    }
}
//部分页面通用
jQuery.myRepeat = {
    clipBoard:function(paperid){
        var idName='paperCodeDiv';
            var html='<div>'+
                        '<table border="1" class="table f-roman" bordercolor="#ccc" width="100%" cellspacing="0" cellpadding="15"><tbody>'+
                            '<tr><td align="center"><b>当前试卷</b></td><td id="pcCurrentPaper">数据载入中</td></tr>'+
                            '<tr><td align="center"><b>答题卡类型</b></td><td id="pcAnswerStyle">数据载入中</td></tr>'+
                            '<tr><td align="center"><b>提取码</b></td><td id="pcSaveCode">数据载入中</td></tr>'+
                        '</tbody></table>'+
                    '</div>';
            $.myDialog.normalMsgBox(idName,'试卷信息',450,html,4);
            $.post(U('Home/Index/getSaveCode'),{'paperID':paperid,'times':Math.random() },function(data){
                if($.myCommon.backLogin(data)==false){
                    $('#'+idName).find('.tcClose').click();
                    return false;
                };
                $('#pcCurrentPaper').html(data['data']['SaveName']);
                $('#pcAnswerStyle').html(data['data']['AnswerStyle']);
                $('#pcSaveCode').html('<span id="copyContent">'+data['data']['SaveCode']+'</span> <a id="copyButton" data-clipboard-target="copyContent" class="blue" href="javascript:;">复制</a>');
                //加载js
                var clip = new ZeroClipboard($('#copyButton'),{
                    moviePath: '/Public/plugin/zeroClipboard/ZeroClipboard.swf'
                });
                clip.on('ready', function(){
                  this.on('aftercopy', function(event){
                    alert('已经复制成功：' + event.data['text/plain']);
                  });
                });

                clip.on('error', function(event){
                  alert('您的浏览器不支持，请手动复制，错误信息：[name="' + event.name + '"]: ' + event.message);
                  ZeroClipboard.destroy();
                });
            });
    },
    // 联考流程  联考流程：组联考试卷 > 下载答题卡 > 考试中心配置考试信息 > 考试 > 扫描答题卡 > 阅卷 > 成绩分析
    examProcess:function(selector,step){
        var currentProgress = '<style>\
#layer-progress { margin:0;background-color: #eaebed; background-color: #fff4c7; padding: 10px; text-align:center;}\
#layer-progress li { display: inline-block; padding: 0 6px; font-size: 14px; }\
#layer-progress li.on-step { color: #04a2e9; }\
#layer-progress li.on-step .icon-arr { color: #04a2e9; }\
#layer-progress .icon-arr { font-size: 19px;padding-left: 4px; font-family: Consolas; }</style>\
<ul id="layer-progress">\
<li><strong>联考流程：</strong></li>\
<li>组联考试卷 <span class="icon-arr">></span></li>\
<li>下载答题卡 <span class="icon-arr">></span></li>\
<li>考试中心配置考试信息 <span class="icon-arr">></span></li>\
<li>考试 <span class="icon-arr">></span></li>\
<li>扫描答题卡 <span class="icon-arr">></span></li>\
<li>阅卷 <span class="icon-arr">></span></li>\
<li>成绩分析</li></ul>';
        $(selector).prepend(currentProgress);
        $("#layer-progress").find("li").eq(step).addClass("on-step");
    }
}
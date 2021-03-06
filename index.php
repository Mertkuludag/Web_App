
<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="MKU"> 
        <title>Web GIS App Demo</title>

<!-- Kaynaklar─▒ ekleyelim -->
        <link rel="stylesheet" href="src/leaflet.css">
        <link rel="stylesheet" href="src/css/bootstrap.css">
        <link rel="stylesheet" href="src/plugins/L.Control.Pan.css">
        <link rel="stylesheet" href="src/plugins/L.Control.Zoomslider.css">
        <link rel="stylesheet" href="src/plugins/L.Control.MousePosition.css">
        <link rel="stylesheet" href="src/plugins/Leaflet.PolylineMeasure.css">
        <link rel="stylesheet" href="src/plugins/easy-button.css">
        <link rel="stylesheet" href="src/plugins/L.Control.Sidebar.css">
        <link rel="stylesheet" href="src/plugins/leaflet-opencage/src/css/L.Control.OpenCageGeocoding.css">
        <link rel="stylesheet" href="src/plugins/MarkerCluster.css">
        <link rel="stylesheet" href="src/plugins/MarkerCluster.Default.css">
       
        <script src="src/leaflet-src.js"></script>
        <script src="src/jquery-3.2.0.min.js"></script>
        <script src="src/plugins/L.Control.Pan.js"></script>
        <script src="src/plugins/L.Control.Zoomslider.js"></script>
        <script src="src/plugins/L.Control.MousePosition.js"></script>
        <script src="src/plugins/Leaflet.PolylineMeasure.js"></script>
        <script src="src/plugins/easy-button.js"></script>
        <script src="src/plugins/L.Control.Sidebar.js"></script>
        <script src="src/plugins/leaflet-opencage/src/js/L.Control.OpenCageGeocoding.js"></script>
        <script src="src/plugins/leaflet-providers.js"></script>
        <script src="src/plugins/leaflet.ajax.min.js"></script>
        <script src="src/plugins/leaflet.markercluster.js"></script>

    <!--    ***************  Leaflet.Draw-->
        
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js"></script>

     <!--    ***************  Leaflet.StyleEditor-->
     <script src="src/plugins/leaflet-styleeditor/javascript/Leaflet.StyleEditor.min.js"></script>
     <link rel="stylesheet" href="src/plugins/leaflet-styleeditor/css/Leaflet.StyleEditor.css">

        <style>
            #mapdiv {
                height:100vh;
            }
            
            .col-xs-12, .col-xs-6, .col-xs-4 {
                padding: 3px;
            }

            #divProject{
                background-color: beige;
            }

            .errorMsg{
                padding: 0;
                text-align: center;
                background-color:darksalmon;
            }



        </style>

    </head>
    <body>

        <div id="side-bar" class="col-md-2">
            <button id="btnLocate" class="btn btn-primary btn-block">Konumuna Git</button><br>
            <div id="divProject" class="col-xs-12">
                <div id="divProjLabel" class="text-center col-xs-12">
                    <h4>Nokta ID ─░le Sorgu</h4>
                </div>
                <div id="divProjectError" class="errorMsg col-xs-12"></div>
                <div id="divFindProject" class="form-group">
                    <div class="col-xs-6">
                        <input type="text" id="txtFindProject" class="form-control" placeholder="Nokta ID'si">
                    </div>
                    <div class="col-xs-6">
                        <button id="btnFindProject" class="btn btn-primary btn-block">
                            Projeyi Bul
                        </button>
                    </div>
                    <div id="divFilterEagle" class="col-xs-12">
                        <div class="col-xs-4">
                            <input type='radio' name='fltAnkara' value='ALL' checked>T├╝m Anketler
                        </div>
                        <div class="col-xs-4">
                            <input type='radio' name='fltAnkara' value='Cevaplandi'>Cevaplananlar
                        </div>
                        <div class="col-xs-4">
                            <input type='radio' name='fltAnkara' value='Yanitsiz'>Cevaps─▒zlar
                        </div>
                    </div>
                    <div class="" id="divProjectData"></div>
                </div>
            </div>

    <!--    <h4>Zoom Level: <span id='zoom-level'></span></h4>--> 
    <!--    <h4>Map Center: <span id='map-center'></span></h4>-->
    <!--    <h4>Mouse Location: <span id='mouse'></span></h4>--> 
            
        </div>

        <div id="sidebar">
        </div>

        <div id="mapdiv" class="col-md-12"></div>
        <script>
            //De─či┼čkenlerimizi tan─▒mlayal─▒m
            var mymap;
            var lyrOSM;
            var lyrDark;
            var lyrImagery;
            var lyrMetal;
            var mrkCurrentLocation;
            var plnBikeRoute;
            var plyParks;
            var fgpLyr;


            //Popup i├žin tan─▒mlama
            var popZocalo;
           /* //yeni zoom kontrol├╝
            var ctlZoom;*/
            //Sayfa sa─č altta bulunan nitelik
            var ctlAttribute;
            var ctlScale;
            var lyrSearch

            //Plugini eklemek i├žin
            var ctlPan;
            var ctlZoomslider;
            var ctlMouse;
            var ctlMeasure
            var ctlEasybtn;
            var ctlSidebar;
            var ctlSearch;
            var ctlLayers;
            var objBasemaps;
            var objLays;

            //Drawer ekleyelim
           var ctlDraw;
           var fgpDrawnItems;

            //Style editor
            var ctlStyle;


            //Vekt├Âr Data
            var markerev;

            //Ajax GeoJson
            var lyrAnkara;
            var pois = new L.featureGroup();
            var arProjectIDs = [];

            //Nokta Cluster
            var lyrMarkerCluster

            
            //DOM manip├╝lasyonlar─▒
            $(document).ready(function(){

                //Altl─▒k Haritalar─▒ Ekleyelim
                mymap = L.map('mapdiv', {center:[39.92936, 32.82333], zoom:8,
                zoomControl:false,  attributionControl:false
                });

                //Altl─▒klar─▒ katman
                lyrOSM = L.tileLayer.provider('OpenStreetMap.DE');
                 lyrImagery = L.tileLayer.provider('Esri.WorldImagery');
                 mymap.addLayer(lyrOSM);


                //Marker Olu┼čturma
                markerev = L.marker([39.92936 , 32.82333], {draggable: true});
                markerev.bindTooltip("Konum Noktas─▒");


                fgpLyr = L.featureGroup([markerev]).addTo(mymap);
                fgpDrawnItems = L.featureGroup().addTo(mymap);

                refreshAnkara();

                objBasemaps = {
                    "OSM": lyrOSM,
                    "Uydu" : lyrImagery
                };

                objLays ={

                };

                ctlLayers = L.control.layers(objBasemaps, objLays).addTo(mymap);

                //Sidebar Plugini Ekleme
                ctlPan = L.control.pan().addTo(mymap);
                ctlZoomslider = L.control.zoomslider({position:'topright'}).addTo(mymap);
                ctlMeasure = L.control.polylineMeasure().addTo(mymap);
                ctlEasybtn = L.easyButton('glyphicon-transfer', function(){
                   ctlSidebar.toggle();
                }).addTo(mymap);
                ctlSidebar= L.control.sidebar('side-bar').addTo(mymap);
                ctlSearch = L.Control.openCageGeocoding({key:'0883bc470c8a4a249869e51d45ab0f3d'
                ,limit: 10 }).addTo(mymap);
                


                ctlAttribute = L.control.attribution ({position:'bottomleft'}).addTo(mymap)
                ctlAttribute.addAttribution('M.K.U');
                ctlAttribute.addAttribution('&copy;<a target="_blank" rel="noopener noreferrer" href="https://github.com/Mertkuludag">GitHub</a>')
                
                ctlScale= L.control.scale({position:'bottomleft'}).addTo(mymap);
                
                ctlMouse = L.control.mousePosition().addTo(mymap);

                //Popup'─▒ kodlama
                //Keepinview ile pop up s├╝rekli ekrana gelir.<h2> Image </h2>
                popZocalo = L.popup({maxWidth:200, keepInView:true});
                popZocalo.setLatLng([40.00784 ,32.97409]);
                //Burada pop up i├žin ba┼čl─▒k ve g├Ârsel olu┼čturuyoruz
                popZocalo.setContent("<h2> Image </h2> <img src='img/zocalo.jpg'width = '200px'>");


              /*  //Her 5000 snde 1 locate'i ├žal─▒┼čt─▒raca─č─▒z
                setInterval(function(){
                    mymap.locate()
                }, 5000)
                */

                /*
                //Bu kod ile shit ile t─▒klad─▒─č─▒m─▒zda zoom seviyemizi g├Âr├╝r├╝z
                mymap.on('click', function(e){
                    if(e.originalEvent.shiftKey){
                        alert(mymap.getZoom())
                    } else{
                        alert(e.latlng.toString());
                    }
                });*/

                mymap.on('contextmenu', function(e){
                var dtCurrentTime = new Date();
                L.marker(e.latlng).addTo(mymap).bindPopup(e.latlng.toString()+"<br>"+dtCurrentTime.toString());
                });

                mymap.on('keypress', function(e){
                    if (e.originalEvent.key == 'l'){
                        mymap.locate();
                    }
                });

                
                
                //Yukar─▒daki 5000snlik kod ile bu kod otomatikle┼čir
                mymap.on('locationfound', function(e){
                    console.log(e);
                    if(mrkCurrentLocation) {
                        mrkCurrentLocation.remove();
                    }
                    //radius ile gps do─čruluk seviyemize g├Âre dairenin size'─▒n─▒ ayarlar─▒z
                    mrkCurrentLocation = L.circle(e.latlng, {radius: e.accuracy/2}).addTo(mymap);
                    mymap.setView(e.latlng,20);
                });

                mymap.on('locationerror', function(e) {
                    console.log(e);
                    alert("Location was not found");
                })

                //Zoomlamay─▒ bitirdi─čimizde bu event tetiklenir
                mymap.on('zoomend', function () {
                    $("#zoom-level").html(mymap.getZoom());
                })

                mymap.on('moveend', function(e){
                    $("#map-center").html(mymap.getCenter().toString());
                });
                
                mymap.on('mousemove', function(e){
                    $("#mouse").html(LatLngToArrayString(e.latlng));
                });

                //marker i├žin event handler
                markerev.on('dragend', function(){
                    markerev.setTooltipContent("Current Location: "+ markerev.getLatLng().toString() +"<br>" + "─░┼če Uzakl─▒k: "+ markerev.getLatLng().distanceTo([39.91625, 32.84436]).toFixed(0) );
                })

                //Lokasyonu bulmak i├žin butonu kullan─▒r─▒m
                $("#btnLocate").click(function(){
                    mymap.locate();
                });

                //T─▒klan─▒ld─▒─č─▒nda girilen konuma zoom yapar, bunu queyde kullan─▒r─▒m se├žilen noktaya yakla┼č─▒r
                $("#btnZocalo").click(function(){
                    mymap.setView([40.00784 ,32.97409] , 15);
                    //Popup'─▒n a├ž─▒lmas─▒ i├žin
                    mymap.openPopup(popZocalo);
                });

                $("#btnIs").click(function(){
                    mymap.setView([39.91625, 32.84436], 19);
                });


                //DRAW
                ctlDraw = new L.Control.Draw({
                    draw:{
                        circle:false,
                        rectangle:false,
                        polyline:false,
                        polygon: false
                    },
                    edit:{
                        featureGroup: fgpDrawnItems
                    }
                    
                });
                ctlDraw.addTo(mymap);

                mymap.on('draw:created', function(e){
                    console.log(e);
                    fgpDrawnItems.addLayer(e.layer)
                });

                //STYLE
                ctlStyle = L.control.styleEditor().addTo(mymap);

            });



            //Jquery B├Âl├╝m├╝
            function returnAnkaraNokta(json, latlng){
                var att = json.properties;
                arProjectIDs.push(att.id.toString());
                if(att.Cevaplama_Durumu == "Cevaplandi"){
                    var clrNokta = 'green';
                }else {
                    clrNokta = 'red';
                }
                
                return L.circleMarker(latlng, {radius:10, color:clrNokta}).bindTooltip(
                    "<h4>Nokta Id'si: "+att.id+"</h4> Cevap Durumu: "+att.Cevaplama_Durumu+"<br>Cevaplayan Cinsiyeti: "+att.Cevaplayan_Cinsiyet);
            };

            
            function filterAnkara(json) {
                var att=json.properties;
                var optFilter = $("input[name=fltAnkara]:checked").val();
                if (optFilter=='ALL') {
                    return true;
                } else {
                    return (att.Cevaplama_Durumu==optFilter);
                }
            }



            $("#txtFindProject").on('keyup paste', function(){
                var val = $("#txtFindProject").val();
                testLayerAttribute(arProjectIDs, val, "Project ID", "#divProjectData", "#divProjectError", "#btnFindProject");
            });
            
            $("#btnFindProject").click(function(){
                var val = $("#txtFindProject").val();
                var lyr = returnLayerByAttribute(lyrAnkara,'id',val);
                if (lyr) {
                    if (lyrSearch) {
                        lyrSearch.remove();
                    }
                    lyrSearch = L.circle(lyr.getLatLng(), {radius:100, color:'purple', weight:10, opacity:0.5, fillOpacity:0}).addTo(mymap);
                    mymap.setView(lyr.getLatLng(), 14);
                    var att = lyr.feature.properties;
                    $("#divProjectData").html("<h4 class='text-center'>Nitelik</h4><h5>Cevaplama Durumu: "+att.Cevaplanma_Durumu+"</h5>");
                    $("#divProjectError").html("");
                } else {
                    $("#divProjectError").html("**** Nokta Bulunamad─▒ ****");
                }
            });

            $("input[name=fltAnkara]").click(function(){
                refreshAnkara()
            });

            function refreshAnkara(){
            $.ajax({url:'load_Nokta.php',
                    success:function(response){
                        arProjectIDs=[];
                        jsnAnkara = JSON.parse(response);
                        if(lyrMarkerCluster){
                            ctlLayers.removeLayer(lyrMarkerCluster);
                            lyrMarkerCluster.remove();
                        }

                        lyrAnkara = L.geoJson(jsnAnkara, 
                        {pointToLayer: returnAnkaraNokta, filter: filterAnkara});
                        lyrMarkerCluster = L.markerClusterGroup();
                        lyrMarkerCluster.addLayer(lyrAnkara); 
                        lyrMarkerCluster.addTo(mymap);
                        ctlLayers.addOverlay(lyrMarkerCluster, 'Anket Noktalar─▒n─▒ Gizle/G├Âster');
                 },
                    error:function(xhr, status, error){
                        alert("ERROR: "+ error);
                    }
                });
            };

            /*
            //Filtreleyerek istenmeyenleri g├Âstermeyebiliriz
            function filterNokta(json){
                var att = json.properties;
                if (att.Cevaplanma_Durumu == true){
                    return true;
                } else { return false ;}

            };*/


            //Koordinatlar─▒ array yapmak
            function LatLngToArrayString(ll){
                return "[" + ll.lat.toFixed(5) + " ," + ll.lng.toFixed (5) + "]";
            }
            function returnLayerByAttribute(lyr,att,val) {
                var arLayers = lyr.getLayers();
                for (i=0;i<arLayers.length-1;i++) {
                    var ftrVal = arLayers[i].feature.properties[att];
                    if (ftrVal==val) {
                        return arLayers[i];
                    }
                }
                return false;
            }
            
            function testLayerAttribute(ar, val, att, fg, err, btn) {
                if (ar.indexOf(val)<0) {
                    $(fg).addClass("has-error");
                    $(err).html("**** "+att+" NOT FOUND ****");
                    $(btn).attr("disabled", true);
                } else {
                    $(fg).removeClass("has-error");
                    $(err).html("");
                    $(btn).attr("disabled", false);
                }
            }
            


        </script>
    </body>
</html>
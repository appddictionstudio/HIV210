<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Keynote</title>
        <link href="KeynoteDHTMLPlayer.css" rel="stylesheet" type="text/css" media="screen"/>
        <meta name="viewport" content="initial-scale = 1.0, minimum-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/prototype.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/DebuggingSupport.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/pdfjs/bcmaps.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/pdfjs/web/compatibility.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/pdfjs/pdf.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/string.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/Utilities.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/KeynoteDHTMLPlayer.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/ShowController.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/TouchController.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/DisplayManager.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/StageManager.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/AnimationManager.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/TextureManager.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/ScriptManager.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/OrientationController.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/KPFObjects.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/SlideManager.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/NavigatorController.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/SlideNumberController.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/HelpPlacardController.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/NarrationManager.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/TSDAnimation.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/gl/KNWebGLShader.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/gl/KNWebGLUtil.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/gl/KNWebGLObjects.js"></script>
        <script type="text/javascript" src="/NMO/NMO1-3/assets/player/gl/KNWebGLParticleObjects.js"></script>
    </head>
    <body id="body" bgcolor="black">
        <div id="background" class="bigBlackSquare">
        </div>
        <div id="previousButton" class="PreviousButtonDisabled">
        </div>
        <div id="nextButton" class="nextButtonDisabled">
        </div>
        <div id="slideCounter">
        </div>
        <span id="helpText"></span>
        <div id="stageArea">
            <div id="stage" class="stage">
            </div>
            <div id="hyperlinkPlane" class="stage">
            </div>
        </div>
        <div id="slideshowNavigator">
        </div>
        <div id="slideNumberControl">
        </div>
        <div id="slideNumberDisplay">
        </div>
        <div id="helpPlacard">
        </div>
        <div id="waitingIndicator">
        	<div id="waitingSpinner">
        	</div>
        </div>
	</body>
</html>

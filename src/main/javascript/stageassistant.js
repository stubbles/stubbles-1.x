/**
 * Stage assistent for help while developing applications with the XML/XSL view engine.
 *
 * @version  $Id$
 */
stubbles.StageAssistant = function()
{
    this.init();
};

stubbles.StageAssistant.prototype =
{
    setup: function()
    {  
        var begin = document.cookie.indexOf('minimized=');
        if (begin != -1) {
            begin += 10;
            this.minimized = document.cookie.substring(begin, document.cookie.indexOf(";", begin));
        }

        if ('true' == this.minimized) {
            this.minimize();
        } else {
            this.open();
        }

        return true;
    },

    close: function() {   
        document.getElementById('stageassistant').style.display = 'none';
        document.getElementById('stageassistant_min').style.display = 'none';
        return true;
    },

    minimize: function() {   
        document.getElementById('stageassistant').style.display = 'none';
        document.getElementById('stageassistant_min').style.display = 'block';
        document.cookie = 'minimized=true';
        return true;
    },

    open: function() {   
        document.getElementById('stageassistant_min').style.display = 'none';
        document.getElementById('stageassistant').style.display = 'block';
        document.getElementById('sa_close').style.display = 'block';
        document.getElementById('sa_minimize').style.display = 'block';
        document.cookie = 'minimized=false';
        return true;
    },

    init: function() {
        this.setup();
    }
};

/*
 * File: app/controller/MainController.js
 *
 * This file was generated by Sencha Architect version 3.1.0.
 * http://www.sencha.com/products/architect/
 *
 * This file requires use of the Ext JS 4.2.x library, under independent license.
 * License of Sencha Architect does not include license for Ext JS 4.2.x. For more
 * details see http://www.sencha.com/license or contact license@sencha.com.
 *
 * This file will be auto-generated each and everytime you save your project.
 *
 * Do NOT hand edit this file.
 */

Ext.define('inficare.controller.MainController', {
    extend: 'Ext.app.Controller',

    views: [
        'InfiMainView',
        'LoginView'
    ],

    refs: [
        {
            ref: 'report',
            selector: 'reportview'
        }
    ],

    onLoginMenuIdClick: function(item, e, eOpts) {
        this.startLogin();

    },

    onLogoffMenuIdClick: function(item, e, eOpts) {
        var ctx = this;

        Ext.Ajax.request({
            url: 'php/login.php',
            method: 'GET',
            params: {
                logoff:true
            },
        	success: function(response){
        		//we must call login with the valid 'this' context.
        		ctx.logoff.call(ctx);
            }
        });

    },

    onCancelLoginButtonIdClick: function(button, e, eOpts) {
        //We will need to check if the login information is correct, but after that, we can go to the main view
        var dialog = this.infiMainView.LoginView;

        dialog.hide();

    },

    onLoginButtonIdClick: function(button, e, eOpts) {
        var ctx = this;
        var form = button.up('#loginFormId').getForm();;

        var userVal = form.findField('user').getValue();
        var passwordVal = form.findField('password').getValue();

        Ext.Ajax.request({
            url: 'php/login.php',
            method: 'GET',
            params: {
                user: userVal,
                password: passwordVal
            },
            success: function(response){
                var res = Ext.JSON.decode(response.responseText, true);

                if (res && res.success)
                {
                    //we must call login with the valid 'this' context.
                    ctx.login.call(ctx);
                }
                else
                {
                    res=Ext.Msg.alert('Erreur', 'Echec d\'authentification');
                }
            }
        });

    },

    onEditRoundMenuIdClick: function(item, e, eOpts) {
        this.editRound();


    },

    onEditPatientMenuIdClick: function(item, e, eOpts) {
        this.editPatients();

    },

    onReportMenuIdClick: function(item, e, eOpts) {
        this.editReports();

    },

    onEditRoundBtnIdClick: function(button, e, eOpts) {
        this.editRound();

    },

    onEditPatientBtnIdClick: function(button, e, eOpts) {
        this.editPatients();

    },

    onReportBtnIdClick: function(button, e, eOpts) {
        this.editReports();

    },

    onLaunch: function() {
        //add 'infimainview' field to the controller to access the infiMainView quickly
        Ext.applyIf(this, {infiMainView: Ext.ComponentQuery.query('infimainview')[0],
            inficareCardWs:Ext.ComponentQuery.query('infimainview #inficareCardWsId')[0],
            mainMenu:Ext.ComponentQuery.query('infimainview toolbar')[0]
        });


        this.startLogin();


    },

    login: function() {
        //We will need to check if the login information is correct, but after that, we can go to the main view
        var cardLayout = this.inficareCardWs.getLayout(),
            dialog = this.infiMainView.LoginView,
            store;

        dialog.hide();

        var treepanel = Ext.ComponentQuery.query('dayeditview #dayVisitTree')[0];
        store = Ext.getStore('VisitEditTreeStore');
        store.getRootNode().removeAll();
        store.load();

        store = Ext.getStore('VisitPatientStore');
        store.load();
        store = Ext.getStore('NurseStore');
        store.load();
        store = Ext.getStore('PatientStore');
        store.load();


        cardLayout.setActiveItem('editRoundViewId');

        //this works but bellow is faster: item.up('menu').down('#loginMenuId').disable();
        this.mainMenu.down('#loginMenuId').disable();
        this.mainMenu.down('#logoffMenuId').enable();
        this.mainMenu.down('#editionMenuId').enable();

        this.mainMenu.down('#editRoundBtnId').enable();
        this.mainMenu.down('#editPatientBtnId').enable();
        this.mainMenu.down('#reportBtnId').enable();

    },

    logoff: function() {
        //We will need to actually logout from the application
        var cardLayout = this.inficareCardWs.getLayout();

        cardLayout.setActiveItem('logoffViewId');

        //this works but bellow is faster: item.up('menu').down('#loginMenuId').disable();
        this.mainMenu.down('#loginMenuId').enable();
        this.mainMenu.down('#logoffMenuId').disable();
        this.mainMenu.down('#editionMenuId').disable();

        this.mainMenu.down('#editRoundBtnId').disable();
        this.mainMenu.down('#editPatientBtnId').disable();
        this.mainMenu.down('#reportBtnId').disable();


    },

    startLogin: function() {
        var dialog = this.infiMainView.LoginView || (this.infiMainView.LoginView = Ext.create('widget.loginview')),
            form = dialog.down('form');


        form.getForm().reset();
        dialog.show();
    },

    editRound: function() {
        var cardLayout = this.inficareCardWs.getLayout();

        cardLayout.setActiveItem('editRoundViewId');

    },

    editPatients: function() {
        var cardLayout = this.inficareCardWs.getLayout();

        cardLayout.setActiveItem('editPatientsListId');

    },

    editReports: function() {
        var cardLayout = this.inficareCardWs.getLayout();

        cardLayout.setActiveItem('reportViewId');

    },

    init: function(application) {
        this.control({
            "infimainview #loginMenuId": {
                click: this.onLoginMenuIdClick
            },
            "infimainview #logoffMenuId": {
                click: this.onLogoffMenuIdClick
            },
            "#cancelLoginButtonId": {
                click: this.onCancelLoginButtonIdClick
            },
            "#loginButtonId": {
                click: this.onLoginButtonIdClick
            },
            "infimainview #editRoundMenuId": {
                click: this.onEditRoundMenuIdClick
            },
            "infimainview #editPatientMenuId": {
                click: this.onEditPatientMenuIdClick
            },
            "infimainview #reportMenuId": {
                click: this.onReportMenuIdClick
            },
            "#editRoundBtnId": {
                click: this.onEditRoundBtnIdClick
            },
            "#editPatientBtnId": {
                click: this.onEditPatientBtnIdClick
            },
            "#reportBtnId": {
                click: this.onReportBtnIdClick
            }
        });
    }

});

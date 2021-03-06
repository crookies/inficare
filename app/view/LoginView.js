/*
 * File: app/view/LoginView.js
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

Ext.define('inficare.view.LoginView', {
    extend: 'Ext.window.Window',
    alias: 'widget.loginview',

    requires: [
        'Ext.form.Panel',
        'Ext.toolbar.Toolbar',
        'Ext.button.Button',
        'Ext.toolbar.Spacer',
        'Ext.form.field.Text'
    ],

    height: 140,
    itemId: 'loginWindowId',
    width: 400,
    layout: 'fit',
    closable: false,
    title: 'Connexion',

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            items: [
                {
                    xtype: 'form',
                    itemId: 'loginFormId',
                    bodyPadding: 10,
                    header: false,
                    title: 'My Form',
                    dockedItems: [
                        {
                            xtype: 'toolbar',
                            dock: 'bottom',
                            layout: {
                                type: 'hbox',
                                pack: 'center'
                            },
                            items: [
                                {
                                    xtype: 'button',
                                    itemId: 'loginButtonId',
                                    glyph: 'xe00b@fcrooks',
                                    text: 'Connexion'
                                },
                                {
                                    xtype: 'tbspacer',
                                    width: 50
                                },
                                {
                                    xtype: 'button',
                                    itemId: 'cancelLoginButtonId',
                                    text: 'Annuler'
                                }
                            ]
                        }
                    ],
                    items: [
                        {
                            xtype: 'textfield',
                            anchor: '100%',
                            itemId: 'userFldId',
                            fieldLabel: 'Nom d\'utilisateur',
                            name: 'user'
                        },
                        {
                            xtype: 'textfield',
                            anchor: '100%',
                            itemId: 'passwordFldId',
                            fieldLabel: 'Mot de passe',
                            name: 'password',
                            inputType: 'password'
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);
    }

});
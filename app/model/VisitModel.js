/*
 * File: app/model/VisitModel.js
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

Ext.define('inficare.model.VisitModel', {
    extend: 'Ext.data.Model',
    alias: 'model.visitmodel',

    requires: [
        'Ext.data.Field',
        'Ext.data.association.HasOne'
    ],
    uses: [
        'inficare.model.NurseModel',
        'inficare.model.PatientModel'
    ],

    idProperty: 'idv',

    fields: [
        {
            name: 'idv',
            type: 'int'
        },
        {
            name: 'datev',
            type: 'date'
        },
        {
            name: 'rectype',
            type: 'int'
        },
        {
            name: 'namev',
            type: 'string'
        },
        {
            name: 'patientid',
            type: 'int'
        },
        {
            name: 'ap',
            type: 'int'
        },
        {
            name: 'nurseid',
            type: 'int'
        },
        {
            name: 'patientinfo',
            type: 'string'
        },
        {
            name: 'patientcare',
            type: 'string'
        },
        {
            name: 'visitinfo',
            type: 'string'
        },
        {
            name: 'index',
            type: 'auto'
        },
        {
            name: 'roundid',
            type: 'int'
        },
        {
            name: 'parentidv',
            type: 'int'
        }
    ],

    hasOne: [
        {
            model: 'inficare.model.NurseModel',
            primaryKey: 'idn',
            foreignKey: 'nurseid'
        },
        {
            model: 'inficare.model.PatientModel',
            primaryKey: 'idp',
            foreignKey: 'patientid'
        }
    ]
});
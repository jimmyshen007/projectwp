/**
 * Created by root on 11/15/16.
 */

export function sendEmail(mail){
    var nodemailer = require('nodemailer');
    var transporter = nodemailer.createTransport({
        service: 'Gmail',
        auth: {
            user: 'info@hi5fang.com', // Your email id
            pass: 'Hi11235813' // Your password
        }
    });

    var text = 'Hello world!';
    var mailOptions = {
        from: 'info@hi5fang.com', // sender address
        to: 'ccyangtianfang@gmail.com', // list of receivers
        subject: 'Email Test', // Subject line
        text: text //, // plaintext body
        // html: '<b>Hello world ✔</b>' // You can choose to send an HTML body instead
    };
    /*transporter.sendMail(mailOptions, function(error, info){
        if(error){
            console.log(error);
            res.json({data: 'error'});
        }else{
            console.log('Message sent: ' + info.response);
            res.json({data: info.response});
        };
    });*/
    return transporter.sendMail(mailOptions);
}

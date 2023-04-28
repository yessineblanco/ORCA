/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.shoppingcart.services;

import com.twilio.Twilio;
import com.twilio.rest.api.v2010.account.Message;
import com.twilio.type.PhoneNumber;
import java.util.List;

/**
 *
 * @author BAZINFO
 */
public class SMSNotifier {
     public static final String ACCOUNT_SID = "AC15fe9151de18222c1df9340b3506eeb5";
    public static final String AUTH_TOKEN = "d9f5250602379d009642e586e9e50519";
  //  public static final String TWILIO_NUMBER = "+16205434078";

    public static void sendSms(String body) {
        Twilio.init(ACCOUNT_SID, AUTH_TOKEN);
        Message message = Message.creator(new com.twilio.type.PhoneNumber("+21629287382"),new com.twilio.type.PhoneNumber("+16073095413"),body).create();
        System.out.println(message.getSid());
    }
    
}

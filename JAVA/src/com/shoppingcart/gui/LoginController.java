package com.shoppingcart.gui;

import com.shoppingcart.MainApp;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;

import java.net.URL;
import java.util.ResourceBundle;

public class LoginController implements Initializable {


    @Override
    public void initialize(URL url, ResourceBundle rb) {
    }


    @FXML
    public void frontend(ActionEvent actionEvent) {
        MainApp.getInstance().loadFront();
    }

    @FXML
    public void backend(ActionEvent actionEvent) {
        MainApp.getInstance().loadBack();
    }
}

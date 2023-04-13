/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import com.jfoenix.controls.JFXButton;
import java.net.URL;
import java.util.ResourceBundle;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.ScrollPane;
import javafx.scene.layout.AnchorPane;
import javafx.scene.layout.FlowPane;

/**
 * FXML Controller class
 *
 * @author paWr
 */
public class SousMetierClientController implements Initializable {

    @FXML
    private AnchorPane anch;
    @FXML
    private JFXButton ButtonCompte;
    @FXML
    private JFXButton ButtonProjet;
    @FXML
    private JFXButton ButtonMetier;
    @FXML
    private JFXButton ButtonAnnonce;
    @FXML
    private JFXButton ButtonEvenement;
    @FXML
    private JFXButton ButtonSignOut;
    @FXML
    private ScrollPane scrollPane;
    @FXML
    private FlowPane flowPane;
    @FXML
    private JFXButton ButtonProjet1;

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        // TODO
    }    

    @FXML
    private void redirecttocompte(ActionEvent event) {
    }

    @FXML
    private void redirecttoProjet(ActionEvent event) {
    }

    @FXML
    private void redirecttometier(ActionEvent event) {
    }

    @FXML
    private void redirectToAnnonce(ActionEvent event) {
    }

    @FXML
    private void redirecttoevenement(ActionEvent event) {
    }

    @FXML
    private void signout(ActionEvent event) {
    }

    @FXML
    private void redirectToMetierCardView(ActionEvent event) {
    }
    
}

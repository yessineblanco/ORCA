package com.shoppingcart.gui.front.products;

import com.shoppingcart.entities.Ligne_commande;
import com.shoppingcart.entities.Products;
import com.shoppingcart.services.Ligne_commandeService;
import com.shoppingcart.services.ProductsService;
import com.shoppingcart.utils.AlertUtils;
import com.shoppingcart.utils.Constants;
import com.shoppingcart.utils.RelationObject;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.geometry.Pos;
import javafx.scene.Parent;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.AnchorPane;
import javafx.scene.layout.HBox;
import javafx.scene.layout.StackPane;
import javafx.scene.layout.VBox;
import javafx.scene.text.Text;

import java.io.IOException;
import java.net.URL;
import java.nio.file.FileSystems;
import java.nio.file.Path;
import java.util.Collections;
import java.util.List;
import java.util.Objects;
import java.util.ResourceBundle;

public class ShowAllController implements Initializable {

    public static Products currentProducts;

    @FXML
    public Text topText;

    public VBox mainVBox;
    @FXML
    public ComboBox<String> sortCB;

    List<Products> listProducts;

    @Override
    public void initialize(URL url, ResourceBundle rb) {
        listProducts = ProductsService.getInstance().getAll();
        sortCB.getItems().addAll("Title", "Price");
        displayData();
    }

    void displayData() {
        mainVBox.getChildren().clear();

        Collections.reverse(listProducts);

        if (!listProducts.isEmpty()) {
            for (Products products : listProducts) {

                mainVBox.getChildren().add(makeProductsModel(products));

            }
        } else {
            StackPane stackPane = new StackPane();
            stackPane.setAlignment(Pos.CENTER);
            stackPane.setPrefHeight(200);
            stackPane.getChildren().add(new Text("Aucune donnée"));
            mainVBox.getChildren().add(stackPane);
        }
    }

    public Parent makeProductsModel(
            Products products
    ) {
        Parent parent = null;
        try {
            parent = FXMLLoader.load(Objects.requireNonNull(getClass().getResource(Constants.FXML_FRONT_MODEL_PRODUCTS)));

            HBox innerContainer = ((HBox) ((AnchorPane) ((AnchorPane) parent).getChildren().get(0)).getChildren().get(0));
            ((Text) innerContainer.lookup("#titleText")).setText("Title : " + products.getTitle());

            ((Text) innerContainer.lookup("#priceText")).setText("Price : " + products.getPrice());
            Path selectedImagePath = FileSystems.getDefault().getPath(products.getImage());
            if (selectedImagePath.toFile().exists()) {
                ((ImageView) innerContainer.lookup("#imageIV")).setImage(new Image(selectedImagePath.toUri().toString()));
            }
            ((Button) innerContainer.lookup("#buyBTN")).setOnAction((a) -> addToCart(products));

        } catch (IOException ex) {
            System.out.println(ex.getMessage());
        }
        return parent;
    }


    @FXML
    public void sort(ActionEvent actionEvent) {
        Constants.compareVar = sortCB.getValue();
        Collections.sort(listProducts);
        displayData();
    }

    private void addToCart(Products products) {
        Ligne_commandeService.getInstance().add(new Ligne_commande(
                new RelationObject(76, ""),
                1,
                new RelationObject(products.getId(), ""),
                products.getPrice()
        ));

        AlertUtils.makeSuccessNotification("Produit ajouté au panier");
    }
}

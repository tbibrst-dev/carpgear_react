const Users = require("../models/user");
const Customers = require("../models/customers");
const transport = require("../utils/nodemailer");
require("dotenv").config();

const addCustomer = async (req, res) => {
  try {
    const {
      name,
      businessName,
      email,
      phone,
      address,
      industry,
      billingAddress,
      description,
      userId,
    } = req.body;
    if (!name || !businessName || !email || !phone || !address || !industry) {
      return res
        .status(400)
        .json({ success: false, message: "Please fill all fields" });
    }
    let existingCustomer = await Customers.findOne({ email });

    if (existingCustomer)
      return res.status(400).json({
        success: false,
        message: "A customer with this email already exist",
      });

    const customer = new Customers({
      name,
      businessName,
      email,
      industry,
      phone,
      address,
      description,
      billingAddress,
      addedBy: userId,
    });
    const newCustomer = await (
      await (await customer.save()).populate("addedBy")
    ).populate({
      path: "addedBy",
      populate: {
        path: "role",
        model: "role",
      },
    });
    res.status(201).json({
      success: true,
      message: "New customer added",
      data: newCustomer,
    });
  } catch (error) {
    console.log(error);
    res.status(500).json({ success: true, message: "Internal server error" });
  }
};

const getCustomers = async (req, res) => {
  const page = req.query.page || 1;
  const pageSize = 5;
  let userId = req.query.userId;
  try {
    let user = await Users.findById(userId).populate("role");

    let customers;
    if (user.role.name === "admin") {
      customers = await Customers.find({})
        .populate("addedBy")
        .populate({
          path: "addedBy",
          populate: {
            path: "role",
            model: "role",
          },
        })
        .skip((page - 1) * pageSize)
        .limit(pageSize);
    } else {
      customers = await Customers.find({ addedBy: userId })
        .skip((page - 1) * pageSize)
        .limit(pageSize);
    }

    if (customers.length === 0)
      return res.json({ success: false, message: "No more data" });

    customers.reverse();
    res.status(200).json({ success: true, data: customers });
  } catch (error) {
    console.log(error);
    res.status(500).json({ success: false, message: "Internal server error" });
  }
};

const sendProposalToCustomer = async (req, res) => {
  try {
    const { link, email, message } = req.body;

    if (!link) {
      return res
        .status(400)
        .json({ success: false, message: "Link is required" });
    }
    const mailOptions = {
      from: process.env.USER_EMAIL,
      to: email,
      subject: `You have a new proposal`,
      html: `<pre>${message}</pre><a href=${link} style="text-decoration: none;">Click here to view the proposal</a>`,
    };
    await transport.sendMail(mailOptions);
    res
      .status(200)
      .json({ success: true, message: "Proposal has been sent to customer" });
  } catch (error) {
    console.log(error);
    res.status(500).json({ success: false, message: "Internal server error" });
  }
};

const updateCustomer = async (req, res) => {
  const { id } = req.params;
  try {
    const {
      name,
      businessName,
      email,
      phone,
      address,
      description,
      industry,
      billingAddress,
    } = req.body;
    let customer = await Customers.findByIdAndUpdate(
      id,
      {
        name,
        email,
        businessName,
        phone,
        industry,
        address,
        description,
        billingAddress,
      },
      { new: true }
    );
    if (!customer) {
      return res.status(400).json({
        success: false,
        message: "Something went wrong while updating the customer",
      });
    }
    res.status(200).json({ success: true, message: "Customer updated" });
  } catch (error) {
    res.status(500).json({ success: false, message: "Internal server error" });
  }
};

const deleteCustomer = async (req, res) => {
  const { id } = req.params;

  try {
    await Customers.findByIdAndDelete(id);
    res.status(200).json({ success: true, message: "Customer deleted" });
  } catch (error) {
    res.status(500).json({ success: false, message: "Internal server error" });
  }
};

const searchCustomers = async (req, res) => {
  const { value } = req.body;

  try {
    const customers = await Customers.find({
      $or: [
        {
          name: { $regex: new RegExp(value, "gi") },
        },
        {
          email: { $regex: new RegExp(value, "gi") },
        },
      ],
    });
    if (customers.length === 0) {
      return res
        .status(400)
        .json({ success: false, message: "No customer found" });
    }
    res.status(200).json({ success: true, data: customers });
  } catch (error) {
    res.status(500).json({ success: false, message: "Internal server error" });
  }
};

const totalCustomerCount = async (req, res) => {
  try {
    const count = await Customers.countDocuments();
    res.status(200).json({ success: true, total: count });
  } catch (error) {
    res.status(500).json({ success: true, message: "Internal server error" });
  }
};

module.exports = {
  addCustomer,
  sendProposalToCustomer,
  updateCustomer,
  deleteCustomer,
  getCustomers,
  searchCustomers,
  totalCustomerCount,
};

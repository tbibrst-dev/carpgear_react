import { useState } from 'react'
import axios from 'axios'
import { toast } from 'react-hot-toast'



const Contact = () => {
  const [contactValue, setContactValues] = useState({
    firstName: "",
    lastName: "",
    email: "",
    phone: "",
    message: ""
  })
  const [isLoading, setIsLoading] = useState(false)

  async function handleContact() {
    const { firstName, lastName, email, phone, message } = contactValue

    if (!firstName) {
      return toast.error("Firstname is required")
    }
    if (!lastName) {
      return toast.error("Lastname is required")
    }

    if (!email) {
      return toast.error("Email is required")
    }

    if (!phone) {
      return toast.error("Phone is required")
    } else if (phone.length < 10) {
      return toast.error("Phone should be of 10 digits")
    }
    if (!message) {
      return toast.error("Message is required")
    }

    setIsLoading(true)
    try {
      const res = await axios.post("/?rest_route=/api/v1/create_contact_entry", {
        first_name: firstName,
        last_name: lastName,
        email,
        message,
        phone
      }, {
        headers: {
          Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`
        }
      })
      if (res.data.success) {
        toast.success("Thank you for contacting us! We will get in touch with you shortly.")
        setContactValues({
          firstName: "",
          lastName: "",
          email: "",
          phone: "",
          message: ""
        })
      }
    } catch (error) {
      console.log(error)
      toast.error("Something went wrong please try again later")
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <div className="contact-us-banner-main">

      {
        isLoading ?
          <div className="basket-loader-container">
            <svg viewBox="25 25 50 50" className="loader-svg">
              <circle r={20} cy={50} cx={50} className="loader" />
            </svg>
          </div>
          : ""
      }

      <div className="contact-us-banner">
        <p>
          Send us a message and we will get back to you as soon as possible.
        </p>
      </div>
      {/* contact us form */}
      <div className="contact-us-form-container">
        <div className="contact-us-form">
          {/* inputs */}
          <div className="contact-us-form-inputs">
            <div className="contact-us-input">
              <label htmlFor="firstName">first name</label>
              <input type="text" placeholder="First Name*" value={contactValue.firstName} onChange={(e: React.ChangeEvent<HTMLInputElement>) => setContactValues({ ...contactValue, firstName: e.target.value })} />
            </div>
            <div className="contact-us-input">
              <label htmlFor="firstName">last name</label>
              <input type="text" placeholder="Last Name*" value={contactValue.lastName} onChange={(e: React.ChangeEvent<HTMLInputElement>) => setContactValues({ ...contactValue, lastName: e.target.value })} />
            </div>
          </div>
          {/* inputs */}
          <div className="contact-us-form-inputs mt-4">
            <div className="contact-us-input">
              <label htmlFor="firstName">email</label>
              <input type="text" placeholder="Email Address*" value={contactValue.email} onChange={(e: React.ChangeEvent<HTMLInputElement>) => setContactValues({ ...contactValue, email: e.target.value })} />
            </div>
            <div className="contact-us-input">
              <label htmlFor="firstName">phone</label>
              <input type="text" placeholder="Phone*" value={contactValue.phone} onChange={(e: React.ChangeEvent<HTMLInputElement>) => setContactValues({ ...contactValue, phone: e.target.value })} />
            </div>
          </div>
          {/* inputs */}
          {/* text area */}
          <div className="contact-us-form-textarea mt-4">
            <div className="label">message</div>
            <div className="textarea">
              <textarea placeholder="Your Message..." value={contactValue.message} onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setContactValues({ ...contactValue, message: e.target.value })} />
            </div>
          </div>
          {/* text area */}
          {/* button */}
          <div className="contact-us-button">
            <button onClick={handleContact} disabled={isLoading}>{"send message"}</button>
          </div>
        </div>
      </div>

    </div>
  );
};

export default Contact;


package homework1;


public class Engineer extends Employee {
    
    /// ATTRIBUTES & FIELDS ///
   
    private String specialty;
    
    /// CONSTRUCTORS ///
    
    public Engineer() {
        super();
        specialty = "";
    }
       
    public Engineer(String n, int i){
        super(n,i);
        specialty = "";
    }
    
    public Engineer(String n, int i, String d, String s){
        super(n, i, d);  
        specialty = s;
    }
    
    /// GETTERS & SETTERS ///
    
    public String getSpecialty(){
        return specialty;
    }
    public void setSpecialty(String specialty){
        this.specialty = specialty;
    }
    
    /// OTHER METHODS ///
    
    @Override
    public void work(){
        System.out.println(getName()+ " is working on " + getSpecialty() + " in the: " +getDepartment());
}
}
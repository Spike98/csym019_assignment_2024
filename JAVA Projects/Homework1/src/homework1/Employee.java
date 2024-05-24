
package homework1;


public class Employee {
   
    /// ATTRIBUTES & FIELDS ///
    
    private String name;
    private int id;
    private String department;
    

    /// CONSTRUCTORS ///
    
    public Employee(){
        name = "blank";
        id = 0;
        department = "blank";
    }
    
    public Employee(String n){
        name = n;
        id = 0;
        department = "blank";        
    }
    
    public Employee(String n, int i){
        name = n;
        id = i;
        department = " ";       
    }
    
    public Employee (String n, int i, String d) { /// έφτιαξα και 4ο constructor, γιατί
        name = n;                                 /// στο 2ο ερώτημα χρειάζεται
        id = i;                                   /// να κάνουμε inherit  με χρήση της super,
        department = d;                           /// και δεν υπήρχε Employee με 3 attributes.
    }
    
    
    /// GETTERS & SETTERS /// 
    
    public String getName() {
        return name;
    }
    
    public void setName(String name){
        this.name = name;
    }
    
    public int getId(){
        return id;
    }
    
    public void setId(int id){
        this.id = id;        
    }
    
    public String getDepartment(){
        return department;
    }
    
    public void setDepartment(String department){
        this.department = department;
    }  
    
    /// OTHER METHODS ///
    
    public void work(){
        System.out.println("Employee: "+name+ " with ID: " + id + " is working in: "+ department);
    }
    
}
